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

class ImageVariantsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('PHP GD with WebP support is required.');
        }
        Storage::fake('public');
    }

    public function test_it_writes_smaller_webp_copies_without_upscaling(): void
    {
        $large = UploadedFile::fake()->image('large.jpg', 1400, 1000)->store('products', 'public');
        $small = UploadedFile::fake()->image('small.png', 300, 300)->store('products', 'public');
        $variants = app(ImageVariants::class);

        $this->assertSame(2, $variants->generate($large));
        $this->assertSame(0, $variants->generate($large), 'Existing variants are left alone.');
        $this->assertSame(0, $variants->generate($small), 'A 300px image needs no smaller copy.');
        $this->assertSame(0, $variants->generate('products/missing.jpg'));

        foreach ([480 => 343, 960 => 686] as $width => $height) {
            $file = Storage::disk('public')->path($variants->variantPath($large, $width));
            $this->assertFileExists($file);
            $this->assertSame([$width, $height, IMAGETYPE_WEBP], array_slice(getimagesize($file), 0, 3));
        }

        $this->assertNull($variants->srcset($small));
        $this->assertNull($variants->srcset(null));
        $srcset = $variants->srcset($large);
        $this->assertStringContainsString('/storage/variants/480/'.preg_replace('/\.jpg$/', '.webp', $large).' 480w', $srcset);
        $this->assertStringContainsString(' 960w', $srcset);
        $this->assertStringNotContainsString('1400w', $srcset);
        $this->assertStringContainsString('/storage/'.$large.' 1400w', $variants->srcset($large, true));
    }

    public function test_uploads_get_variants_when_saved_and_lose_them_when_replaced_or_deleted(): void
    {
        $product = $this->product();
        $first = UploadedFile::fake()->image('first.jpg', 1200, 1200)->store('products', 'public');
        $second = UploadedFile::fake()->image('second.jpg', 1200, 1200)->store('products', 'public');
        $variants = app(ImageVariants::class);

        $image = ProductImage::create(['product_id' => $product->id, 'image' => $first, 'is_main' => true]);
        Storage::disk('public')->assertExists($variants->variantPath($first, 480));
        Storage::disk('public')->assertExists($variants->variantPath($first, 960));

        $image->update(['image' => $second]);
        Storage::disk('public')->assertMissing($variants->variantPath($first, 480));
        Storage::disk('public')->assertExists($variants->variantPath($second, 480));

        $image->delete();
        Storage::disk('public')->assertMissing($variants->variantPath($second, 480));
        Storage::disk('public')->assertMissing($variants->variantPath($second, 960));
    }

    public function test_the_command_builds_variants_for_images_already_in_the_catalogue(): void
    {
        $path = UploadedFile::fake()->image('tea.jpg', 1100, 1100)->store('categories', 'public');
        $category = Category::create(['name' => 'Tea', 'slug' => 'tea', 'status' => true, 'image' => $path]);
        $variants = app(ImageVariants::class);
        $variants->forget($category->image);
        Storage::disk('public')->assertMissing($variants->variantPath($path, 480));

        $this->artisan('images:variants')->assertSuccessful();

        Storage::disk('public')->assertExists($variants->variantPath($path, 480));
        Storage::disk('public')->assertExists($variants->variantPath($path, 960));
    }

    private function product(): Product
    {
        $category = Category::create(['name' => 'Pantry', 'slug' => 'pantry', 'status' => true]);

        return Product::create(['category_id' => $category->id, 'name' => 'Hill Honey', 'slug' => 'hill-honey', 'sku' => 'HON-1', 'price' => 400, 'stock_quantity' => 10, 'status' => true]);
    }
}
