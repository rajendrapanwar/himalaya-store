<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ServiceablePincode;
use App\Services\StorefrontCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $version = StorefrontCache::version();
        $categories = Cache::remember("storefront:categories:v{$version}", now()->addMinutes(10), fn () => Category::where('status', 1)
            ->whereHas('products', fn ($query) => $query->where('status', true))
            ->withCount(['products' => fn ($query) => $query->where('status', true)])
            ->orderBy('name')->get());
        $origins = Cache::remember("storefront:origins:v{$version}", now()->addMinutes(10), fn () => Product::query()
            ->where('status', true)
            ->whereNotNull('origin_region')
            ->where('origin_region', '!=', '')
            ->distinct()
            ->orderBy('origin_region')
            ->pluck('origin_region'));

        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = $categories->firstWhere('slug', $request->string('category')->toString());
            abort_unless($selectedCategory !== null, 404);
        }

        $productsQuery = Product::with(['category', 'images'])
            ->withCount(['reviews as approved_reviews_count' => fn ($query) => $query->where('is_approved', true)])
            ->withAvg(['reviews as approved_reviews_avg' => fn ($query) => $query->where('is_approved', true)], 'rating')
            ->where('status', 1)
            ->when($request->category, function ($query, $categorySlug) {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('origin_region', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('min_price'), fn ($query) => $query->whereRaw('COALESCE(sale_price, price) >= ?', [max(0, $request->float('min_price'))]))
            ->when($request->filled('max_price'), fn ($query) => $query->whereRaw('COALESCE(sale_price, price) <= ?', [max(0, $request->float('max_price'))]))
            ->when($request->string('availability')->toString() === 'in_stock', fn ($query) => $query->whereRaw('(stock_quantity - reserved_quantity) > 0'))
            ->when($request->string('availability')->toString() === 'out_of_stock', fn ($query) => $query->whereRaw('(stock_quantity - reserved_quantity) <= 0'))
            ->when($request->boolean('sale'), fn ($query) => $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'))
            ->when($request->boolean('featured'), fn ($query) => $query->where('is_featured', true))
            ->when($request->filled('origin'), fn ($query) => $query->where('origin_region', $request->string('origin')->toString()))
            ->when($request->integer('rating') >= 1 && $request->integer('rating') <= 5, fn ($query) => $query->whereRaw('(SELECT AVG(rating) FROM reviews WHERE reviews.product_id = products.id AND reviews.is_approved = 1) >= ?', [$request->integer('rating')]))
            ->when($request->string('weight')->toString(), function ($query, $weight) {
                match ($weight) {
                    'under_250' => $query->where('weight', '<', 250),
                    '250_499' => $query->whereBetween('weight', [250, 499.99]),
                    '500_999' => $query->whereBetween('weight', [500, 999.99]),
                    '1000_plus' => $query->where('weight', '>=', 1000),
                    default => null,
                };
            });

        match ($request->string('sort')->toString()) {
            'price_low' => $productsQuery->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_high' => $productsQuery->orderByRaw('COALESCE(sale_price, price) DESC'),
            'name' => $productsQuery->orderBy('name'),
            default => $productsQuery->latest(),
        };

        $cacheKey = 'storefront:products:v'.$version.':'.hash('xxh128', json_encode([
            'category' => $request->string('category')->toString(),
            'search' => $request->string('search')->toString(),
            'sort' => $request->string('sort')->toString(),
            'min_price' => $request->string('min_price')->toString(),
            'max_price' => $request->string('max_price')->toString(),
            'availability' => $request->string('availability')->toString(),
            'sale' => $request->boolean('sale'),
            'featured' => $request->boolean('featured'),
            'origin' => $request->string('origin')->toString(),
            'rating' => $request->integer('rating'),
            'weight' => $request->string('weight')->toString(),
            'page' => max(1, $request->integer('page', 1)),
        ], JSON_THROW_ON_ERROR));
        $products = Cache::remember($cacheKey, now()->addMinutes(5), fn () => $productsQuery->paginate(12))->withQueryString();

        $wishlistProductIds = auth()->check()
            ? auth()->user()->wishlist()->pluck('products.id')->all()
            : [];

        return view('frontend.products.index', compact('products', 'categories', 'origins', 'selectedCategory', 'wishlistProductIds'));
    }

    public function show($slug)
    {
        $catalogue = Cache::remember('storefront:product:v'.StorefrontCache::version().':'.$slug, now()->addMinutes(5), function () use ($slug) {
            $product = Product::with(['category', 'images', 'reviews' => fn ($q) => $q->where('is_approved', true)->with('user')])
                ->where('slug', $slug)->where('status', 1)->firstOrFail();
            $relatedProducts = Product::with(['category', 'images'])
                ->withCount(['reviews as approved_reviews_count' => fn ($query) => $query->where('is_approved', true)])
                ->withAvg(['reviews as approved_reviews_avg' => fn ($query) => $query->where('is_approved', true)], 'rating')
                ->where('status', 1)->where('category_id', $product->category_id)->where('id', '!=', $product->id)
                ->latest()->take(4)->get();

            return compact('product', 'relatedProducts');
        });
        ['product' => $product, 'relatedProducts' => $relatedProducts] = $catalogue;

        $wishlistSaved = auth()->check() && auth()->user()->wishlist()->where('products.id', $product->id)->exists();
        $canReview = auth()->check() && OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($query) => $query
                ->where('user_id', auth()->id())
                ->where(fn ($status) => $status->whereNotNull('paid_at')->orWhereNotNull('delivered_at')))
            ->exists();

        return view('frontend.products.show', compact('product', 'relatedProducts', 'wishlistSaved', 'canReview'));
    }

    public function bundles()
    {
        return view('frontend.bundles.index', [
            'bundles' => Cache::remember('storefront:bundles:v'.StorefrontCache::version(), now()->addMinutes(5), fn () => Bundle::with(['products' => fn ($query) => $query->where('status', true)])->where('is_active', true)->get()),
        ]);
    }

    public function pincode(Request $request)
    {
        $data = $request->validate(['pincode' => 'required|regex:/^[1-9][0-9]{5}$/']);
        $serviceability = ServiceablePincode::where('pincode', $data['pincode'])->where('is_active', true)->first();
        if (! $serviceability) {
            if ($request->expectsJson()) {
                return response()->json([
                    'serviceable' => false,
                    'message' => 'We do not currently deliver to this pincode. Contact us for help with a special order.',
                ]);
            }

            return back()->with('error', 'We do not currently deliver to this pincode. Contact us for assistance with a special order.');
        }

        session(['delivery_pincode' => $serviceability->pincode]);
        $cod = $serviceability->cod_available ? 'COD is available.' : 'Online payment is required.';

        if ($request->expectsJson()) {
            return response()->json([
                'serviceable' => true,
                'message' => "Delivery is available to {$serviceability->city}, {$serviceability->state}.",
                'location' => "{$serviceability->city}, {$serviceability->state}",
                'estimate' => "{$serviceability->estimated_days_min}–{$serviceability->estimated_days_max} business days",
                'cod_available' => $serviceability->cod_available,
                'payment_message' => $cod,
                'shipping_amount' => (float) $serviceability->shipping_amount,
            ]);
        }

        return back()->with('success', "Delivery is available to {$serviceability->city}, {$serviceability->state}. Estimated {$serviceability->estimated_days_min}–{$serviceability->estimated_days_max} days. {$cod}");
    }
}
