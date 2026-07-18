<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $products = $request->user()->wishlist()
            ->with(['images', 'category'])
            ->withCount(['reviews as approved_reviews_count' => fn ($query) => $query->where('is_approved', true)])
            ->withAvg(['reviews as approved_reviews_avg' => fn ($query) => $query->where('is_approved', true)], 'rating')
            ->paginate(12);

        return view('frontend.wishlist.index', compact('products'));
    }

    public function store(Request $request, Product $product)
    {
        abort_unless($product->status, 404);
        $request->user()->wishlist()->syncWithoutDetaching([$product->id]);

        if ($request->expectsJson()) {
            return response()->json([
                'saved' => true,
                'message' => 'Saved to your wishlist.',
                'wishlist_count' => $request->user()->wishlist()->count(),
            ]);
        }

        return back()->with('success', 'Saved to your wishlist.');
    }

    public function destroy(Request $request, Product $product)
    {
        $request->user()->wishlist()->detach($product->id);

        if ($request->expectsJson()) {
            return response()->json([
                'saved' => false,
                'message' => 'Removed from your wishlist.',
                'wishlist_count' => $request->user()->wishlist()->count(),
            ]);
        }

        return back()->with('success', 'Removed from your wishlist.');
    }
}
