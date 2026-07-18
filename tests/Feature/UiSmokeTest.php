<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_storefront_pages_render_without_template_errors(): void
    {
        $this->seed(DatabaseSeeder::class);
        $product = Product::firstOrFail();

        foreach (['/', '/products', "/products/{$product->slug}", '/bundles', '/cart', '/about', '/contact', '/blog', '/rewards-and-referrals', '/faq', '/shipping-policy', '/return-policy', '/privacy-policy', '/terms', '/editorial-policy', '/login', '/register', '/forgot-password'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_customer_and_admin_interface_pages_render(): void
    {
        $this->seed(DatabaseSeeder::class);
        $customer = User::factory()->create(['email_verified_at' => now()]);
        foreach (['/account', '/my-orders', '/wishlist'] as $path) {
            $this->actingAs($customer)->get($path)->assertOk();
        }

        $admin = User::factory()->create(['is_admin' => true, 'role' => 'administrator', 'email_verified_at' => now()]);
        $session = ['admin_2fa_verified_at' => now()->timestamp];
        foreach (['/admin/dashboard', '/admin/orders', '/admin/products', '/admin/products/create', '/admin/inventory', '/admin/categories', '/admin/categories/create', '/admin/coupons', '/admin/coupons/create', '/admin/bundles', '/admin/bundles/create', '/admin/journals', '/admin/journals/create', '/admin/hero-slides', '/admin/hero-slides/create', '/admin/reviews', '/admin/returns', '/admin/customers', '/admin/contact-messages', '/admin/serviceability', '/admin/serviceability/create', '/admin/reports', '/admin/team', '/admin/activity'] as $path) {
            $this->actingAs($admin)->withSession($session)->get($path)->assertOk();
        }
    }

    public function test_public_information_pages_use_readable_image_heroes(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['/about', '/contact', '/faq', '/shipping-policy', '/return-policy', '/privacy-policy', '/terms', '/editorial-policy', '/rewards-and-referrals', '/bundles'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('page-hero', false)
                ->assertSee('assets/img/editorial/', false)
                ->assertSee('bg-gradient-to-r', false)
                ->assertSee('object-cover', false);
        }
    }
}
