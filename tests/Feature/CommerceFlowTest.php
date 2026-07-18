<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ServiceablePincode;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\OrderLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommerceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cod_checkout_persists_coupon_loyalty_shipping_and_inventory(): void
    {
        [$user, $product] = $this->customerAndProduct();
        $user->update(['loyalty_points' => 200]);
        ServiceablePincode::create(['pincode' => '248001', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'shipping_amount' => 49, 'estimated_days_min' => 1, 'estimated_days_max' => 3, 'cod_available' => true, 'is_active' => true]);
        $coupon = Coupon::create(['code' => 'SAVE10', 'type' => 'percent', 'value' => 10, 'minimum_amount' => 0, 'per_user_limit' => 1, 'is_active' => true]);

        $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 2])->assertRedirect(route('cart.index'));
        $this->post(route('cart.coupon.apply'), ['code' => 'save10'])->assertRedirect();
        $this->post(route('checkout.placeOrder'), $this->checkoutData(['loyalty_points' => 100]))->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('SAVE10', $order->coupon_code);
        $this->assertSame('100.00', $order->discount_amount);
        $this->assertSame('10.00', $order->loyalty_discount_amount);
        $this->assertSame('49.00', $order->shipping_amount);
        $this->assertSame('939.00', $order->total_amount);
        $this->assertSame(8, $product->fresh()->stock_quantity);
        $this->assertSame(0, $product->fresh()->reserved_quantity);
        $this->assertDatabaseHas('coupon_redemptions', ['coupon_id' => $coupon->id, 'order_id' => $order->id, 'status' => 'redeemed']);
        $this->assertSame(100, $user->fresh()->loyalty_points);
    }

    public function test_bundle_checkout_decrements_component_quantities(): void
    {
        [$user, $product] = $this->customerAndProduct();
        ServiceablePincode::create(['pincode' => '248001', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'shipping_amount' => 0, 'estimated_days_min' => 1, 'estimated_days_max' => 3, 'cod_available' => true, 'is_active' => true]);
        $bundle = Bundle::create(['name' => 'Tea Box', 'slug' => 'tea-box', 'price' => 799, 'is_active' => true]);
        $bundle->products()->attach($product->id, ['quantity' => 2]);

        $this->actingAs($user)->post(route('cart.bundles.add', $bundle), ['quantity' => 1])->assertRedirect(route('cart.index'));
        $this->post(route('checkout.placeOrder'), $this->checkoutData())->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'bundle_id' => $bundle->id, 'product_name' => 'Tea Box']);
        $this->assertSame(8, $product->fresh()->stock_quantity);
    }

    public function test_failed_payment_releases_reserved_inventory(): void
    {
        [$user, $product] = $this->customerAndProduct();
        $order = Order::create(['order_number' => 'HS-STRIPE-FAIL', 'user_id' => $user->id, 'subtotal' => 500, 'total_amount' => 500, 'payment_method' => 'stripe', 'payment_status' => 'pending', 'order_status' => 'pending', 'payment_expires_at' => now()->addMinutes(30)]);
        $this->app->make(InventoryService::class)->reserve($order, [$product->id => 3], now()->addMinutes(30));
        $this->assertSame(3, $product->fresh()->reserved_quantity);

        $this->app->make(OrderLifecycleService::class)->failPayment($order);
        $this->assertSame(0, $product->fresh()->reserved_quantity);
        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('inventory_reservations', ['order_id' => $order->id, 'status' => 'released']);
    }

    public function test_stripe_webhook_route_is_not_rejected_by_csrf(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);
        $this->postJson(route('stripe.webhook'), [])->assertStatus(400);
    }

    public function test_delivery_pincode_checker_returns_inline_json_details(): void
    {
        ServiceablePincode::create(['pincode' => '248001', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'shipping_amount' => 49, 'estimated_days_min' => 1, 'estimated_days_max' => 3, 'cod_available' => true, 'is_active' => true]);

        $this->postJson(route('pincode.check'), ['pincode' => '248001'])
            ->assertOk()
            ->assertJson(['serviceable' => true, 'location' => 'Dehradun, Uttarakhand', 'estimate' => '1–3 business days', 'cod_available' => true, 'shipping_amount' => 49]);

        $this->postJson(route('pincode.check'), ['pincode' => '110001'])
            ->assertOk()
            ->assertJson(['serviceable' => false]);

        $this->postJson(route('pincode.check'), ['pincode' => '123'])
            ->assertUnprocessable();
    }

    public function test_checkout_prefills_saved_address_and_quotes_shipping_in_the_total(): void
    {
        [$user, $product] = $this->customerAndProduct();
        Address::create([
            'user_id' => $user->id, 'first_name' => 'Asha', 'last_name' => 'Sharma',
            'email' => 'asha@example.test', 'phone' => '9876543210',
            'address_line_1' => '14 Rajpur Road', 'address_line_2' => 'Near Clock Tower',
            'city' => 'Dehradun', 'state' => 'Uttarakhand', 'pincode' => '248001', 'country' => 'India',
        ]);
        ServiceablePincode::create(['pincode' => '248001', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'shipping_amount' => 49, 'estimated_days_min' => 1, 'estimated_days_max' => 3, 'cod_available' => true, 'is_active' => true]);

        $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 1]);
        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('value="Asha"', false)
            ->assertSee('value="Sharma"', false)
            ->assertSee('value="9876543210"', false)
            ->assertSee('value="14 Rajpur Road"', false)
            ->assertSee('₹49.00')
            ->assertSee('₹549.00');

        $this->postJson(route('checkout.quote'), ['pincode' => '248001', 'loyalty_points' => 0])
            ->assertOk()
            ->assertJson(['shipping' => 49.0, 'total' => 549.0, 'cod_available' => true]);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::create(['order_number' => 'HS-PRIVATE', 'user_id' => $owner->id, 'subtotal' => 100, 'total_amount' => 100, 'payment_method' => 'cod', 'payment_status' => 'pending', 'order_status' => 'pending']);
        $this->actingAs($other)->get(route('orders.show', $order))->assertForbidden();
    }

    private function customerAndProduct(): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::create(['name' => 'Tea', 'slug' => 'tea', 'status' => true]);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Mountain Tea', 'slug' => 'mountain-tea', 'sku' => 'TEA-1', 'price' => 500, 'stock_quantity' => 10, 'status' => true]);

        return [$user, $product];
    }

    private function checkoutData(array $overrides = []): array
    {
        return $overrides + ['first_name' => 'Asha', 'last_name' => 'Sharma', 'email' => 'asha@example.test', 'phone' => '9876543210', 'address_line_1' => 'Rajpur Road', 'address_line_2' => null, 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'pincode' => '248001', 'customer_note' => null, 'payment_method' => 'cod', 'loyalty_points' => 0];
    }
}
