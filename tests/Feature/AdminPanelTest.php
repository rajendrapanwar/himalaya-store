<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Models\ServiceablePincode;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_form_field_has_an_accessible_name(): void
    {
        $admin = $this->staff('administrator');
        $this->staff('manager');
        $customer = User::factory()->create();
        $category = Category::create(['name' => 'Pantry', 'slug' => 'pantry', 'status' => true]);
        $product = $this->product($category);
        $bundle = Bundle::create(['name' => 'Breakfast Box', 'slug' => 'breakfast-box', 'price' => 900, 'is_active' => true]);
        $bundle->products()->attach($product->id, ['quantity' => 1]);
        $pincode = ServiceablePincode::create(['pincode' => '248001', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'shipping_amount' => 49, 'estimated_days_min' => 2, 'estimated_days_max' => 5, 'cod_available' => true, 'is_active' => true]);
        $order = $this->order($customer, ['order_status' => 'delivered', 'payment_status' => 'paid']);
        Review::create(['product_id' => $product->id, 'user_id' => $customer->id, 'rating' => 5, 'title' => 'Lovely', 'body' => 'Tastes like home.', 'moderation_status' => 'pending']);
        ReturnRequest::create(['order_id' => $order->id, 'user_id' => $customer->id, 'reason' => 'damaged', 'details' => 'The jar arrived cracked.', 'status' => 'requested']);
        ContactMessage::create(['name' => 'Asha', 'email' => 'asha@example.test', 'phone' => '9876543210', 'subject' => 'Bulk order', 'message' => 'Do you ship in bulk?', 'status' => 'new']);

        $pages = [
            route('admin.dashboard'), route('admin.orders.index'), route('admin.orders.show', $order), route('admin.returns.index'),
            route('admin.products.index'), route('admin.products.create'), route('admin.products.edit', $product), route('admin.inventory.index'),
            route('admin.categories.index'), route('admin.categories.create'), route('admin.categories.edit', $category),
            route('admin.bundles.index'), route('admin.bundles.create'), route('admin.bundles.edit', $bundle),
            route('admin.journals.index'), route('admin.journals.create'), route('admin.hero-slides.index'), route('admin.hero-slides.create'),
            route('admin.customers.index'), route('admin.customers.show', $customer), route('admin.coupons.index'), route('admin.coupons.create'),
            route('admin.reviews.index'), route('admin.contact-messages.index'),
            route('admin.serviceability.index'), route('admin.serviceability.create'), route('admin.serviceability.edit', $pincode),
            route('admin.reports.index'), route('admin.team.index'), route('admin.activity.index'), route('admin.maintenance.index'),
        ];

        // Guard against a checker that silently matches nothing.
        $this->assertSame(['email'], $this->fieldsWithoutAName('<section class="admin-content"><input name="email"><label for="a">A</label><input id="a" name="named"></section>'));

        foreach ($pages as $page) {
            $html = $this->visit($admin, $page)->assertOk()->getContent();
            $this->assertSame([], $this->fieldsWithoutAName($html), "Fields without a label on {$page}");
        }
    }

    public function test_admin_pages_load_the_small_admin_script_instead_of_the_storefront_bundle(): void
    {
        $html = $this->visit($this->staff('administrator'), route('admin.dashboard'))->assertOk()->getContent();

        // Built assets are hashed; a running Vite dev server serves the source path instead.
        $this->assertMatchesRegularExpression('#(assets/admin-[\w-]+|resources/js/admin)\.js#', $html);
        $this->assertDoesNotMatchRegularExpression('#(assets/app-[\w-]+|resources/js/app)\.js#', $html);
    }

    public function test_editing_a_hidden_category_keeps_it_hidden(): void
    {
        $category = Category::create(['name' => 'Old Range', 'slug' => 'old-range', 'status' => false]);

        $html = $this->visit($this->staff('administrator'), route('admin.categories.edit', $category))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<option value="0"\s+selected[^>]*>Hidden/', $html);
        $this->assertDoesNotMatchRegularExpression('/<option value="1"\s+selected/', $html);
    }

    public function test_the_category_list_reaches_categories_beyond_the_first_page(): void
    {
        foreach (range(1, 12) as $number) {
            Category::create(['name' => "Range {$number}", 'slug' => "range-{$number}", 'status' => true]);
        }

        $this->visit($this->staff('administrator'), route('admin.categories.index'))
            ->assertOk()
            ->assertSee('12 categories configured')
            ->assertSee('page=2', false);
    }

    public function test_the_order_page_only_offers_status_moves_that_are_allowed(): void
    {
        $admin = $this->staff('administrator');
        $order = $this->order(User::factory()->create(), ['order_status' => 'shipped', 'courier_name' => 'Delhivery', 'tracking_number' => 'TRK1']);

        $html = $this->visit($admin, route('admin.orders.show', $order))->assertOk()->getContent();

        $this->assertStringContainsString('<option value="delivered"', $html);
        $this->assertStringContainsString('<option value="cancelled"', $html);
        $this->assertStringNotContainsString('<option value="pending"', $html);
        $this->assertStringNotContainsString('<option value="processing"', $html);

        // The server still refuses a move the form no longer offers.
        $this->actingAs($admin)->withSession($this->verified())
            ->put(route('admin.orders.update', $order), ['order_status' => 'pending'])
            ->assertStatus(422);
        $this->assertSame('shipped', $order->fresh()->order_status);
    }

    public function test_order_page_actions_match_what_each_staff_role_may_do(): void
    {
        $order = $this->order(User::factory()->create(), ['order_status' => 'processing']);

        // Support can read orders and open customers, but cannot change an order.
        $this->visit($this->staff('support'), route('admin.orders.show', $order))
            ->assertOk()
            ->assertDontSee('Save fulfilment')
            ->assertSee('Customer profile')
            ->assertSee('https://wa.me/919876543210', false);

        // Fulfilment can change an order, but has no access to the customer directory.
        $this->visit($this->staff('fulfillment'), route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Save fulfilment')
            ->assertDontSee('Customer profile');
    }

    private function staff(string $role): User
    {
        return User::factory()->create(['is_admin' => $role === 'administrator', 'role' => $role]);
    }

    /** @return array{admin_2fa_verified_at: int} */
    private function verified(): array
    {
        return ['admin_2fa_verified_at' => now()->timestamp];
    }

    private function visit(User $user, string $url): TestResponse
    {
        return $this->actingAs($user)->withSession($this->verified())->get($url);
    }

    private function product(Category $category): Product
    {
        return Product::create(['category_id' => $category->id, 'name' => 'Hill Honey', 'slug' => 'hill-honey', 'sku' => 'HON-1', 'price' => 400, 'stock_quantity' => 10, 'status' => true]);
    }

    /** @param array<string, mixed> $overrides */
    private function order(User $customer, array $overrides = []): Order
    {
        $address = Address::create(['user_id' => $customer->id, 'first_name' => 'Asha', 'last_name' => 'Rawat', 'email' => 'asha@example.test', 'phone' => '9876543210', 'address_line_1' => '18 Mall Road', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'pincode' => '248001', 'country' => 'India']);
        $order = Order::create(array_merge(['order_number' => 'HS-ADMIN-'.fake()->unique()->numerify('####'), 'user_id' => $customer->id, 'address_id' => $address->id, 'subtotal' => 400, 'shipping_amount' => 49, 'total_amount' => 449, 'payment_method' => 'cod', 'payment_status' => 'pending', 'order_status' => 'pending'], $overrides));
        OrderItem::create(['order_id' => $order->id, 'product_name' => 'Hill Honey', 'product_sku' => 'HON-1', 'quantity' => 1, 'price' => 400, 'total' => 400]);

        return $order;
    }

    /**
     * Form fields inside the admin content area that a screen reader would
     * announce without a name: no <label for>, wrapping <label> or aria-label.
     *
     * @return list<string>
     */
    private function fieldsWithoutAName(string $html): array
    {
        $document = new DOMDocument;
        @$document->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new DOMXPath($document);
        $missing = [];

        foreach ($xpath->query('//section[contains(@class,"admin-content")]//*[self::input or self::select or self::textarea]') as $field) {
            if (! $field instanceof DOMElement || $field->getAttribute('type') === 'hidden') {
                continue;
            }
            $id = $field->getAttribute('id');
            $named = $field->getAttribute('aria-label') !== ''
                || ($id !== '' && $xpath->query('//label[@for="'.$id.'"]')->length > 0)
                || $xpath->query('ancestor::label', $field)->length > 0;
            if (! $named) {
                $missing[] = $field->getAttribute('name') ?: $field->nodeName;
            }
        }

        return $missing;
    }
}
