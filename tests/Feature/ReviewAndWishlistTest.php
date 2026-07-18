<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewAndWishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_customer_can_save_a_product_and_submit_a_review(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Tea', 'slug' => 'tea', 'status' => true]);
        $product = Product::create([
            'category_id' => $category->id, 'name' => 'Herbal Tea', 'slug' => 'herbal-tea',
            'price' => 299, 'stock_quantity' => 10, 'status' => true,
        ]);

        $this->actingAs($user)->post(route('wishlist.store', $product))->assertRedirect();
        $this->assertDatabaseHas('wishlists', ['user_id' => $user->id, 'product_id' => $product->id]);

        $order = Order::create(['order_number' => 'HS-REVIEW-1', 'user_id' => $user->id, 'subtotal' => 299, 'total_amount' => 299, 'payment_method' => 'stripe', 'payment_status' => 'paid', 'order_status' => 'processing', 'paid_at' => now()]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'price' => 299, 'total' => 299]);

        $this->actingAs($user)->post(route('reviews.store', $product), [
            'rating' => 5, 'title' => 'Excellent', 'body' => 'Fresh and aromatic herbal tea.',
        ])->assertRedirect();
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'product_id' => $product->id, 'rating' => 5, 'is_approved' => false, 'verified_purchase' => true]);
    }

    public function test_inactive_product_cannot_be_added_to_wishlist(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Hidden', 'slug' => 'hidden', 'status' => true]);
        $product = Product::create([
            'category_id' => $category->id, 'name' => 'Unavailable Tea', 'slug' => 'unavailable-tea',
            'price' => 299, 'stock_quantity' => 10, 'status' => false,
        ]);

        $this->actingAs($user)->post(route('wishlist.store', $product))->assertNotFound();
        $this->assertDatabaseMissing('wishlists', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_wishlist_can_be_toggled_with_json_without_a_page_redirect(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Pulses', 'slug' => 'pulses', 'status' => true]);
        $product = Product::create([
            'category_id' => $category->id, 'name' => 'Mountain Rajma', 'slug' => 'mountain-rajma',
            'price' => 349, 'stock_quantity' => 12, 'status' => true,
        ]);

        $this->actingAs($user)->postJson(route('wishlist.store', $product))
            ->assertOk()->assertJson(['saved' => true, 'wishlist_count' => 1]);

        $this->actingAs($user)->deleteJson(route('wishlist.destroy', $product))
            ->assertOk()->assertJson(['saved' => false, 'wishlist_count' => 0]);

        $this->assertDatabaseMissing('wishlists', ['user_id' => $user->id, 'product_id' => $product->id]);
    }
}
