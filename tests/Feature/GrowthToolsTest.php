<?php

namespace Tests\Feature;

use App\Events\ContactMessageSubmitted;
use App\Mail\CartReminder;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class GrowthToolsTest extends TestCase
{
    use RefreshDatabase;

    // ── Analytics ──────────────────────────────────────────────────────

    public function test_analytics_stay_off_until_an_id_is_configured(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $response->assertDontSee('googletagmanager.com', false)->assertDontSee('fbevents.js', false);
        $this->assertStringNotContainsString('googletagmanager', (string) $response->headers->get('Content-Security-Policy'));
    }

    public function test_configured_analytics_load_for_visitors_but_not_for_staff(): void
    {
        config(['services.analytics.ga4_id' => 'G-TEST12345', 'services.analytics.meta_pixel_id' => '1234567890']);

        $response = $this->get(route('home'))->assertOk()
            ->assertSee('G-TEST12345', false)
            ->assertSee('https://www.googletagmanager.com/gtag/js', false)
            ->assertSee('https://connect.facebook.net/en_US/fbevents.js', false);
        $policy = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString('https://*.googletagmanager.com', $policy);
        $this->assertStringContainsString('https://*.google-analytics.com', $policy);
        $this->assertStringContainsString('https://connect.facebook.net', $policy);

        $staff = User::factory()->create(['is_admin' => true, 'role' => 'administrator']);
        $this->actingAs($staff)->get(route('home'))->assertOk()->assertDontSee('G-TEST12345', false);
    }

    public function test_a_malformed_analytics_id_is_ignored(): void
    {
        config(['services.analytics.ga4_id' => 'G-1"><script>alert(1)</script>']);

        $this->get(route('home'))->assertOk()->assertDontSee('alert(1)', false)->assertDontSee('googletagmanager.com', false);
    }

    public function test_shopping_events_are_reported_when_analytics_are_on(): void
    {
        config(['services.analytics.ga4_id' => 'G-TEST12345']);
        $customer = User::factory()->create();
        $product = $this->product(['price' => 400]);

        $this->get(route('products.show', $product->slug))->assertOk()
            ->assertSee('data-analytics-event', false)
            ->assertSee('"name":"view_item"', false)
            ->assertSee('"item_id":"HON-1"', false);

        $this->actingAs($customer)->postJson(route('cart.add', $product), ['quantity' => 2])
            ->assertOk()
            ->assertJsonPath('analytics.items.0.item_id', 'HON-1')
            ->assertJsonPath('analytics.items.0.quantity', 2)
            ->assertJsonPath('analytics.value', 800);

        $paid = $this->order($customer, ['order_number' => 'HS-TRACK-1', 'payment_method' => 'cod']);
        $this->actingAs($customer)->get(route('checkout.success', $paid))->assertOk()
            ->assertSee('"name":"purchase"', false)
            ->assertSee('"transaction_id":"HS-TRACK-1"', false)
            ->assertSee('"once":"purchase:HS-TRACK-1"', false);

        // An online order that has not been paid is not a sale yet.
        $unpaid = $this->order($customer, ['order_number' => 'HS-TRACK-2', 'payment_method' => 'stripe', 'payment_status' => 'pending']);
        $this->actingAs($customer)->get(route('checkout.success', $unpaid))->assertOk()->assertDontSee('"name":"purchase"', false);
    }

    public function test_add_to_cart_sends_no_analytics_data_when_tracking_is_off(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('cart.add', $this->product()), ['quantity' => 1])
            ->assertOk()
            ->assertJsonPath('added', true)
            ->assertJsonPath('analytics', null);
    }

    // ── Bulk orders ────────────────────────────────────────────────────

    public function test_a_bulk_order_enquiry_reaches_the_contact_requests_inbox(): void
    {
        Event::fake([ContactMessageSubmitted::class]);

        $this->get(route('bulk-orders'))->assertOk()->assertSee('Request a quote');

        $this->post(route('bulk-orders.send'), [
            'name' => 'Asha Rawat', 'company' => 'Hill Tech', 'email' => 'asha@example.test', 'phone' => '9876543210',
            'quantity' => 120, 'budget' => 800, 'needed_by' => now()->addMonth()->toDateString(), 'message' => 'Diwali gifts for the team.',
        ])->assertRedirect()->assertSessionHas('success')->assertSessionHas('bulk_enquiry_sent');

        $enquiry = ContactMessage::firstOrFail();
        $this->assertSame('Bulk order enquiry', $enquiry->subject);
        $this->assertSame('9876543210', $enquiry->phone);
        $this->assertStringContainsString('Quantity: 120 gifts', $enquiry->message);
        $this->assertStringContainsString('Budget per gift: ₹800', $enquiry->message);
        $this->assertStringContainsString('Company or event: Hill Tech', $enquiry->message);
        $this->assertStringContainsString('Diwali gifts for the team.', $enquiry->message);
        Event::assertDispatched(ContactMessageSubmitted::class, fn ($event) => $event->contactMessageId === $enquiry->id);
    }

    public function test_a_bulk_order_enquiry_needs_real_details(): void
    {
        // The form allows two submissions a minute; this test needs three.
        $this->withoutMiddleware(ThrottleRequests::class);
        $valid = ['name' => 'Asha', 'email' => 'asha@example.test', 'phone' => '9876543210', 'quantity' => 50];

        $this->post(route('bulk-orders.send'), ['quantity' => 3] + $valid)->assertSessionHasErrors('quantity');
        $this->post(route('bulk-orders.send'), ['phone' => ''] + $valid)->assertSessionHasErrors('phone');
        // The hidden "website" field is a trap for spam bots.
        $this->post(route('bulk-orders.send'), $valid + ['website' => 'https://spam.example'])->assertSessionHasErrors('website');
        $this->assertSame(0, ContactMessage::count());
    }

    // ── Buy again ──────────────────────────────────────────────────────

    public function test_buy_again_refills_the_cart_at_todays_price_within_stock(): void
    {
        $customer = User::factory()->create();
        $honey = $this->product(['price' => 450, 'stock_quantity' => 2]);                     // price went up since the order
        $tea = $this->product(['name' => 'Hill Tea', 'slug' => 'hill-tea', 'sku' => 'TEA-1']);
        $gone = $this->product(['name' => 'Old Pickle', 'slug' => 'old-pickle', 'sku' => 'PIC-1', 'status' => false]);
        $order = $this->order($customer, [], [[$honey, 5, 400], [$tea, 1, 400], [$gone, 1, 300]]);

        $this->actingAs($customer)->post(route('orders.buy-again', $order))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('success', fn ($message) => str_contains($message, '2 items are back in your cart') && str_contains($message, '1 could not be added'));

        $cart = Cart::where('user_id', $customer->id)->firstOrFail();
        $this->assertSame(2, $cart->items()->count());
        $honeyLine = $cart->items()->where('product_id', $honey->id)->firstOrFail();
        $this->assertSame(2, $honeyLine->quantity, 'Quantity is limited to what is in stock.');
        $this->assertEquals(450, $honeyLine->price, 'The cart uses the current price, not the old order price.');
    }

    public function test_buy_again_is_only_for_the_customer_who_placed_the_order(): void
    {
        $order = $this->order(User::factory()->create(), [], [[$this->product(), 1, 400]]);

        $this->post(route('orders.buy-again', $order))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->post(route('orders.buy-again', $order))->assertForbidden();
        $this->assertSame(0, CartItem::count());
    }

    public function test_buy_again_says_so_when_nothing_is_available(): void
    {
        $customer = User::factory()->create();
        $order = $this->order($customer, [], [[$this->product(['stock_quantity' => 0]), 1, 400]]);

        $this->actingAs($customer)->from(route('orders'))->post(route('orders.buy-again', $order))
            ->assertRedirect(route('orders'))
            ->assertSessionHas('error');
    }

    // ── Basket reminders ───────────────────────────────────────────────

    public function test_basket_reminders_are_off_by_default(): void
    {
        Mail::fake();
        $this->abandonedCart(User::factory()->create());

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_a_basket_left_for_a_few_hours_gets_one_reminder(): void
    {
        Mail::fake();
        config(['commerce.cart_reminders.enabled' => true]);
        $customer = User::factory()->create(['name' => 'Asha Rawat']);
        $cart = $this->abandonedCart($customer);

        $this->artisan('carts:send-reminders')->assertSuccessful();
        $this->artisan('carts:send-reminders')->assertSuccessful();

        Mail::assertQueued(CartReminder::class, 1);
        Mail::assertQueued(CartReminder::class, fn (CartReminder $mail) => $mail->hasTo($customer->email) && $mail->cart->is($cart));
        $this->assertNotNull($cart->fresh()->reminded_at);

        $html = (new CartReminder($cart->fresh()->load(['user', 'items.product', 'bundleItems.bundle'])))->render();
        $this->assertStringContainsString('Namaste Asha Rawat', $html);
        $this->assertStringContainsString('Hill Honey × 2', $html);
        $this->assertStringContainsString('basket-reminders/unsubscribe', $html);
    }

    public function test_reminders_skip_people_who_should_not_be_emailed(): void
    {
        Mail::fake();
        config(['commerce.cart_reminders.enabled' => true]);
        $product = $this->product();

        $this->abandonedCart(User::factory()->unverified()->create(), $product);                                   // address not confirmed
        $this->abandonedCart(User::factory()->create(['is_admin' => true, 'role' => 'administrator']), $product);  // staff
        $optedOut = User::factory()->create();
        $optedOut->forceFill(['cart_reminders_unsubscribed_at' => now()])->save();
        $this->abandonedCart($optedOut, $product);                                                                 // asked us to stop
        $this->abandonedCart(User::factory()->create(), $product, hoursAgo: 1);                                    // still shopping
        $this->abandonedCart(User::factory()->create(), $product, hoursAgo: 24 * 10);                              // too old
        $this->abandonedCart(User::factory()->create(), $this->product(['slug' => 'sold-out', 'sku' => 'OUT-1', 'stock_quantity' => 0])); // nothing to buy

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_the_unsubscribe_link_works_without_signing_in_and_must_be_signed(): void
    {
        $customer = User::factory()->create();

        $this->get(route('cart-reminders.unsubscribe', $customer))->assertForbidden();
        $this->assertNull($customer->fresh()->cart_reminders_unsubscribed_at);

        $this->get(URL::signedRoute('cart-reminders.unsubscribe', ['user' => $customer->id]))
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');
        $this->assertNotNull($customer->fresh()->cart_reminders_unsubscribed_at);
    }

    // ── Helpers ────────────────────────────────────────────────────────

    /** @param array<string, mixed> $overrides */
    private function product(array $overrides = []): Product
    {
        $category = Category::firstOrCreate(['slug' => 'pantry'], ['name' => 'Pantry', 'status' => true]);

        return Product::create($overrides + ['category_id' => $category->id, 'name' => 'Hill Honey', 'slug' => 'hill-honey', 'sku' => 'HON-1', 'price' => 400, 'stock_quantity' => 10, 'status' => true]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @param  list<array{0: Product, 1: int, 2: int|float}>  $lines  product, quantity, price paid
     */
    private function order(User $customer, array $overrides = [], array $lines = []): Order
    {
        $address = Address::create(['user_id' => $customer->id, 'first_name' => 'Asha', 'last_name' => 'Rawat', 'email' => 'asha@example.test', 'phone' => '9876543210', 'address_line_1' => '18 Mall Road', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'pincode' => '248001', 'country' => 'India']);
        $order = Order::create($overrides + ['order_number' => 'HS-GROW-'.fake()->unique()->numerify('####'), 'user_id' => $customer->id, 'address_id' => $address->id, 'subtotal' => 400, 'shipping_amount' => 49, 'total_amount' => 449, 'payment_method' => 'cod', 'payment_status' => 'pending', 'order_status' => 'pending']);
        foreach ($lines ?: [[null, 1, 400]] as [$product, $quantity, $price]) {
            OrderItem::create(['order_id' => $order->id, 'product_id' => $product?->id, 'product_name' => $product?->name ?? 'Hill Honey', 'product_sku' => $product?->sku ?? 'HON-1', 'quantity' => $quantity, 'price' => $price, 'total' => $price * $quantity]);
        }

        return $order;
    }

    private function abandonedCart(User $customer, ?Product $product = null, int $hoursAgo = 5): Cart
    {
        $product ??= $this->product();
        $this->travelTo(now()->subHours($hoursAgo));
        $cart = Cart::create(['user_id' => $customer->id, 'session_id' => 'session-'.$customer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2, 'price' => $product->price]);
        $this->travelBack();

        return $cart;
    }
}
