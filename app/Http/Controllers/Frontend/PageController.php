<?php

namespace App\Http\Controllers\Frontend;

use App\Events\ContactMessageSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\JournalPost;
use App\Models\Product;
use App\Services\StorefrontCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    private const SITEMAP_PAGE_SIZE = 1000;

    public function about()
    {
        return view('frontend.pages.about');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'email' => 'required|email|max:255', 'phone' => 'nullable|string|max:20', 'subject' => 'required|string|max:120', 'message' => 'required|string|max:3000', 'website' => 'nullable|max:0']);
        unset($data['website']);
        $message = ContactMessage::create($data + ['user_id' => $request->user()?->id]);
        ContactMessageSubmitted::dispatch($message->id);

        return back()->with('success', 'Thank you — your message has been received. We will reply shortly.');
    }

    public function blog(Request $request)
    {
        $posts = JournalPost::published()
            ->when($request->filled('topic'), fn ($query) => $query->where('category', $request->string('topic')))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();
        abort_if($posts->currentPage() > $posts->lastPage(), 404);

        $topics = JournalPost::published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('frontend.pages.blog', compact('posts', 'topics'));
    }

    public function post(string $slug)
    {
        $post = JournalPost::published()->where('slug', $slug)->firstOrFail();
        $relatedPosts = JournalPost::published()->whereKeyNot($post->getKey())
            ->when($post->category, fn ($query) => $query->where('category', $post->category))
            ->latest('published_at')->take(3)->get();

        if ($relatedPosts->count() < 3) {
            $relatedPosts = $relatedPosts->concat(JournalPost::published()
                ->whereKeyNot($post->getKey())
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->latest('published_at')->take(3 - $relatedPosts->count())->get());
        }

        return view('frontend.pages.post', compact('post', 'relatedPosts'));
    }

    public function feed()
    {
        $posts = JournalPost::published()->latest('published_at')->take(30)->get();
        $lastModified = $posts->max('updated_at');

        $response = response()->view('feed', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=1800');

        if ($lastModified) {
            $response->header('Last-Modified', $lastModified->toRfc7231String());
        }

        return $response;
    }

    public function sitemapIndex()
    {
        $entries = Cache::remember('storefront:sitemap-index:v'.StorefrontCache::version(), now()->addHour(), function () {
            $categories = Category::query()
                ->where('status', true)
                ->whereHas('products', fn ($query) => $query->where('status', true))
                ->with(['products' => fn ($query) => $query->where('status', true)->select(['id', 'category_id', 'updated_at'])])
                ->orderBy('slug')
                ->get(['id', 'slug', 'updated_at']);

            $sitemaps = collect([
                ['loc' => route('sitemaps.pages'), 'lastmod' => null],
                ['loc' => route('sitemaps.categories'), 'lastmod' => $categories->flatMap(fn (Category $category) => [$category->updated_at, $category->products->max('updated_at')])->filter()->max()],
                ['loc' => route('sitemaps.journal'), 'lastmod' => JournalPost::published()->max('updated_at')],
            ]);

            foreach ($categories as $category) {
                $pages = (int) ceil($category->products->count() / self::SITEMAP_PAGE_SIZE);
                foreach (range(1, max(1, $pages)) as $page) {
                    $sitemaps->push([
                        'loc' => route('sitemaps.products', ['category' => $category->slug, 'page' => $page]),
                        'lastmod' => $category->products->max('updated_at'),
                    ]);
                }
            }

            return $sitemaps->all();
        });

        return $this->xmlView('sitemaps.index', compact('entries'));
    }

    public function pagesSitemap()
    {
        $entries = Cache::remember('storefront:sitemap-pages:v'.StorefrontCache::version(), now()->addHour(), function () {
            $entries = collect([
                route('home'), route('products.index'), route('about'), route('blog'), route('rewards'),
                route('contact'), route('faq'), route('shipping.policy'), route('returns.policy'),
                route('privacy.policy'), route('terms'), route('editorial.policy'),
            ])->map(fn ($url) => ['loc' => $url]);

            return $entries->push([
                'loc' => route('bundles.index'),
                'lastmod' => Bundle::where('is_active', true)->max('updated_at'),
            ])->all();
        });

        return $this->xmlView('sitemaps.urls', compact('entries'));
    }

    public function categoriesSitemap()
    {
        $entries = Cache::remember('storefront:sitemap-categories:v'.StorefrontCache::version(), now()->addHour(), fn () => Category::query()
            ->where('status', true)
            ->whereHas('products', fn ($query) => $query->where('status', true))
            ->with(['products' => fn ($query) => $query->where('status', true)->select(['id', 'category_id', 'updated_at'])])
            ->orderBy('slug')
            ->get(['name', 'slug', 'image', 'updated_at'])
            ->map(fn (Category $category) => [
                'loc' => route('products.index', ['category' => $category->slug]),
                'lastmod' => collect([$category->updated_at, $category->products->max('updated_at')])->filter()->max(),
                'images' => $category->image ? [['loc' => asset('storage/'.$category->image), 'title' => $category->name]] : [],
            ])->all());

        return $this->xmlView('sitemaps.urls', compact('entries'));
    }

    public function journalSitemap()
    {
        $entries = Cache::remember('storefront:sitemap-journal:v'.StorefrontCache::version(), now()->addHour(), fn () => JournalPost::published()
            ->latest('updated_at')
            ->get(['title', 'slug', 'featured_image', 'updated_at'])
            ->map(fn (JournalPost $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at,
                'images' => $post->image_url ? [['loc' => $post->image_url, 'title' => $post->title]] : [],
            ])->all());

        return $this->xmlView('sitemaps.urls', compact('entries'));
    }

    public function productsSitemap(string $category, int $page)
    {
        $categoryModel = Category::query()
            ->where('slug', $category)
            ->where('status', true)
            ->whereHas('products', fn ($query) => $query->where('status', true))
            ->firstOrFail();

        $productCount = $categoryModel->products()->where('status', true)->count();
        $lastPage = (int) ceil($productCount / self::SITEMAP_PAGE_SIZE);
        abort_if($page > $lastPage, 404);

        $entries = Cache::remember("storefront:sitemap-products:{$category}:{$page}:v".StorefrontCache::version(), now()->addHour(), fn () => Product::query()
            ->with('images')
            ->where('category_id', $categoryModel->id)
            ->where('status', true)
            ->orderBy('id')
            ->forPage($page, self::SITEMAP_PAGE_SIZE)
            ->get(['id', 'name', 'slug', 'updated_at'])
            ->map(fn (Product $product) => [
                'loc' => route('products.show', $product->slug),
                'lastmod' => $product->updated_at,
                'images' => $product->images->map(fn ($image) => [
                    'loc' => asset('storage/'.$image->image),
                    'title' => $product->name,
                ])->all(),
            ])->all());

        return $this->xmlView('sitemaps.urls', compact('entries'));
    }

    private function xmlView(string $view, array $data)
    {
        return response()->view($view, $data)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function robots()
    {
        $directives = app()->environment('production')
            ? [
                'User-agent: *', 'Allow: /', 'Disallow: /admin/', 'Disallow: /stripe/',
                'Disallow: /*?*search=', 'Disallow: /*?*sort=', 'Disallow: /*?*min_price=',
                'Disallow: /*?*max_price=', 'Disallow: /*?*availability=', 'Disallow: /*?*sale=',
                'Disallow: /*?*featured=', 'Disallow: /*?*origin=', 'Disallow: /*?*rating=',
                'Disallow: /*?*weight=',
            ]
            : ['User-agent: *', 'Disallow: /'];
        $content = implode("\n", [...$directives, 'Sitemap: '.route('sitemap'), '']);

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
