<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorefrontImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_cards_offer_the_smaller_copies_and_defer_the_hover_photo(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('PHP GD with WebP support is required.');
        }
        Storage::fake('public');

        $category = Category::create(['name' => 'Pantry', 'slug' => 'pantry', 'status' => true]);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Hill Honey', 'slug' => 'hill-honey', 'sku' => 'HON-1', 'price' => 400, 'stock_quantity' => 10, 'status' => true]);
        $main = UploadedFile::fake()->image('main.jpg', 1200, 1200)->store('products', 'public');
        $hover = UploadedFile::fake()->image('hover.jpg', 1200, 1200)->store('products', 'public');
        ProductImage::create(['product_id' => $product->id, 'image' => $main, 'is_main' => true]);
        ProductImage::create(['product_id' => $product->id, 'image' => $hover, 'is_main' => false]);

        $html = $this->get(route('products.index'))->assertOk()->getContent();

        $this->assertStringContainsString('srcset="'.e(app(ImageVariants::class)->srcset($main)).'"', $html);
        // The hover photo has no src until the first hover, so phones never download it.
        $this->assertStringContainsString('data-hover-src="'.asset('storage/'.$hover).'"', $html);
        $this->assertStringNotContainsString('src="'.asset('storage/'.$hover).'"', str_replace('data-hover-src=', 'data-hover=', $html));
    }
}
