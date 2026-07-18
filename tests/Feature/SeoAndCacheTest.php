<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\StorefrontCache;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_and_private_pages_emit_appropriate_search_metadata(): void
    {
        $this->seed(DatabaseSeeder::class);
        $product = Product::firstOrFail();

        $home = $this->get(route('home'))
            ->assertOk()
            ->assertSee('index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')
            ->assertSee('https://schema.org', false)
            ->assertSee('OnlineStore')
            ->assertSee('MerchantReturnPolicy');
        $home->assertSee('"@context":"https://schema.org"', false)
            ->assertDontSee('__contextArgs', false)
            ->assertSee('Follow our journey')
            ->assertSee('Follow Pahadi Roots on Instagram');

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('Product')
            ->assertSee('priceCurrency')
            ->assertSee('BreadcrumbList')
            ->assertSee('data-share', false)
            ->assertSee('Share '.$product->name);

        $this->get(route('cart.index'))->assertOk()->assertSee('noindex, nofollow')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get(route('products.index', ['search' => 'honey']))->assertOk()->assertSee('noindex, follow');

        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin/')
            ->assertSee(route('sitemap'));
    }

    public function test_category_pages_have_distinct_metadata_and_invalid_categories_are_real_404s(): void
    {
        $this->seed(DatabaseSeeder::class);
        $category = Category::query()->whereHas('products')->firstOrFail();

        $this->get(route('products.index', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee($category->name.' from the Himalayas')
            ->assertSee(route('products.index', ['category' => $category->slug]), false)
            ->assertSee('CollectionPage');

        $this->get(route('products.index', ['category' => 'not-a-real-collection']))->assertNotFound();
    }

    public function test_sitemap_contains_canonical_content_and_product_images(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', false)
            ->assertSee(route('editorial.policy'))
            ->assertSee('<image:image>', false);
    }

    public function test_catalogue_filters_change_results_and_remain_out_of_the_index(): void
    {
        $this->seed(DatabaseSeeder::class);
        $saleProduct = Product::query()->whereNotNull('sale_price')->latest()->firstOrFail();
        $regularProduct = Product::query()->whereNull('sale_price')->latest()->firstOrFail();

        $this->get(route('products.index', ['sale' => 1]))
            ->assertOk()
            ->assertSee($saleProduct->name)
            ->assertDontSee($regularProduct->name)
            ->assertSee('noindex, follow')
            ->assertSee('Filter products');

        $this->get(route('products.index', ['origin' => $saleProduct->origin_region]))
            ->assertOk()
            ->assertSee($saleProduct->name);

        $saleProduct->update(['stock_quantity' => 0, 'reserved_quantity' => 0]);
        $this->get(route('products.index', ['availability' => 'out_of_stock']))
            ->assertOk()
            ->assertSee($saleProduct->name);
    }

    public function test_catalogue_cache_version_changes_after_catalogue_mutation(): void
    {
        $before = StorefrontCache::version();
        Category::create(['name' => 'Fresh', 'slug' => 'fresh', 'status' => true]);

        $this->assertGreaterThan($before, StorefrontCache::version());
    }
}
