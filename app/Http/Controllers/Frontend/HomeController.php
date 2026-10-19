<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\JournalPost;
use App\Models\Product;
use App\Models\Review;
use App\Services\StorefrontCache;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $catalogue = Cache::remember('storefront:home:v4:'.StorefrontCache::version(), now()->addMinutes(5), function () {
            $categories = Category::where('status', 1)
                ->whereHas('products', fn ($query) => $query->where('status', true))
                ->withCount(['products' => fn ($query) => $query->where('status', true)])
                ->orderBy('name')->take(9)->get();

            $productQuery = fn () => Product::with(['category', 'images'])
                ->withCount(['reviews as approved_reviews_count' => fn ($query) => $query->where('is_approved', true)])
                ->withAvg(['reviews as approved_reviews_avg' => fn ($query) => $query->where('is_approved', true)], 'rating')
                ->where('status', 1);

            $featuredProducts = $productQuery()->where('is_featured', 1)->latest()->take(8)->get();
            $latestProducts = $productQuery()->latest()->take(10)->get();
            $saleProducts = $productQuery()->whereNotNull('sale_price')->latest()->take(8)->get();
            $featuredBundle = Bundle::with(['products' => fn ($query) => $query->where('status', true)])
                ->where('is_active', true)->latest()->first();
            $journalPosts = JournalPost::published()->orderByDesc('is_featured')->latest('published_at')->take(3)->get();
            $heroSlides = HeroSlide::visible()->orderBy('sort_order')->orderBy('id')->get();
            $testimonials = Review::with(['user:id,name', 'product:id,name,slug'])
                ->where('is_approved', true)
                ->where('rating', '>=', 4)
                ->latest()
                ->take(6)
                ->get();
            $stats = [
                'products' => Product::where('status', 1)->count(),
                'collections' => Category::where('status', 1)->whereHas('products', fn ($query) => $query->where('status', true))->count(),
                'regions' => Product::where('status', 1)->whereNotNull('origin_region')->where('origin_region', '!=', '')->distinct()->count('origin_region'),
            ];

            return compact('categories', 'featuredProducts', 'latestProducts', 'saleProducts', 'featuredBundle', 'journalPosts', 'heroSlides', 'testimonials', 'stats');
        });

        $heroSlides = $catalogue['heroSlides'];
        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([new HeroSlide([
                'eyebrow' => 'From Uttarakhand, with care',
                'title' => 'Good food has a place, a maker and a story.',
                'description' => 'Traceable mountain produce from small producers, packed thoughtfully in Dehradun.',
                'image_path' => 'assets/img/hero/himalayan-collection-v2.webp',
                'image_alt' => 'A collection of Himalayan pantry products',
                'primary_label' => 'Shop the collection',
                'primary_url' => '/products',
                'secondary_label' => 'Our sourcing story',
                'secondary_url' => '/about',
                'text_theme' => 'light',
                'text_position' => 'left',
            ])]);
        }

        $wishlistProductIds = auth()->check()
            ? auth()->user()->wishlist()->pluck('products.id')->all()
            : [];

        return view('frontend.home', ['heroSlides' => $heroSlides, 'wishlistProductIds' => $wishlistProductIds] + $catalogue);
    }
}
