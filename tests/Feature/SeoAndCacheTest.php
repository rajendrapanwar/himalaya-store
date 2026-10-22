<?php

namespace Tests\Feature;

use App\Http\Middleware\ClearStaleBrowserCache;
use App\Models\Category;
use App\Models\JournalPost;
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
            ->assertSee('Disallow: /')
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

    public function test_sitemap_index_separates_canonical_content_by_catalogue_category(): void
    {
        $this->seed(DatabaseSeeder::class);
        $category = Category::query()->whereHas('products')->firstOrFail();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<sitemapindex', false)
            ->assertSee(route('sitemaps.pages'))
            ->assertSee(route('sitemaps.categories'))
            ->assertSee(route('sitemaps.journal'))
            ->assertSee(route('sitemaps.products', ['category' => $category->slug, 'page' => 1]));

        $this->get(route('sitemaps.pages'))
            ->assertOk()
            ->assertSee(route('editorial.policy'));

        $this->get(route('sitemaps.categories'))
            ->assertOk()
            ->assertSee(route('products.index', ['category' => $category->slug]), false);

        $this->get(route('sitemaps.products', ['category' => $category->slug, 'page' => 1]))
            ->assertOk()
            ->assertSee('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', false)
            ->assertSee('<image:image>', false);

        $this->get(route('sitemaps.journal'))
            ->assertOk()
            ->assertSee('<urlset', false);

        $this->get(route('sitemaps.products', ['category' => 'not-a-category', 'page' => 1]))->assertNotFound();
        $this->get(route('sitemaps.products', ['category' => $category->slug, 'page' => 999]))->assertNotFound();
    }

    public function test_catalogue_seo_overrides_and_focus_phrases_are_stored_without_obsolete_meta_keywords(): void
    {
        $category = Category::factory()->create([
            'seo_title' => 'Mountain Pulses from Uttarakhand | Pahadi Roots',
            'meta_description' => 'Shop traceable mountain pulses selected from Uttarakhand farms, with useful origin, pack and cooking details.',
            'focus_keyword' => 'Uttarakhand mountain pulses',
        ]);
        $product = Product::factory()->for($category)->create([
            'seo_title' => 'Organic Gahat Dal from Uttarakhand | Pahadi Roots',
            'meta_description' => 'Buy traceable gahat dal from Uttarakhand with clear producer, origin, pack, delivery and ingredient information.',
            'focus_keyword' => 'organic gahat dal Uttarakhand',
        ]);

        $this->get(route('products.index', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee('<title>'.$category->seo_title.'</title>', false)
            ->assertSee($category->meta_description)
            ->assertDontSee('name="keywords"', false);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('<title>'.$product->seo_title.'</title>', false)
            ->assertSee($product->meta_description)
            ->assertDontSee('name="keywords"', false);
    }

    public function test_journal_feed_and_pagination_canonical_are_machine_readable(): void
    {
        for ($index = 1; $index <= 10; $index++) {
            JournalPost::create([
                'title' => 'Mountain Guide '.$index,
                'slug' => 'mountain-guide-'.$index,
                'author_name' => 'Rajendra Panwar',
                'excerpt' => 'A useful guide to Himalayan produce, origin, handling and everyday kitchen use.',
                'content' => str_repeat('Useful mountain sourcing and storage guidance. ', 15),
                'is_published' => true,
                'published_at' => now()->subDays($index),
            ]);
        }

        $this->get(route('blog', ['page' => 2]))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('blog', ['page' => 2]).'">', false);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<rss version="2.0"', false)
            ->assertSee('Mountain Guide 1');
    }

    public function test_structured_data_safely_encodes_admin_managed_content(): void
    {
        $product = Product::factory()->create([
            'name' => 'Safe Mountain Product',
            'short_description' => '</script><script>alert("schema")</script>',
        ]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertDontSee('</script><script>alert', false)
            ->assertSee('\\u003C/script\\u003E', false);
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

    public function test_browsers_are_told_once_to_drop_stale_cached_redirects(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertHeader('Clear-Site-Data', '"cache"')
            ->assertCookie(ClearStaleBrowserCache::COOKIE, ClearStaleBrowserCache::VERSION);

        $this->withCookie(ClearStaleBrowserCache::COOKIE, ClearStaleBrowserCache::VERSION)
            ->get(route('login'))
            ->assertOk()
            ->assertHeaderMissing('Clear-Site-Data');

        $this->post(route('login'), ['email' => 'nobody@example.test', 'password' => 'wrong'])
            ->assertHeaderMissing('Clear-Site-Data');
        $this->getJson(route('products.suggestions', ['q' => 'honey']))
            ->assertHeaderMissing('Clear-Site-Data');
    }
}
