<?php

namespace App\Http\Controllers\Frontend;

use App\Events\ReviewSubmitted;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $verified = OrderItem::where('product_id', $product->id)->whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id)->where(fn ($q) => $q->whereNotNull('paid_at')->orWhereNotNull('delivered_at')))->exists();
        if (! $verified) {
            throw ValidationException::withMessages(['review' => 'Reviews can be submitted after a verified purchase of this product.']);
        }
        $data = $request->validate(['rating' => 'required|integer|between:1,5', 'title' => 'nullable|string|max:150', 'body' => 'required|string|min:10|max:2000']);
        $review = $product->reviews()->updateOrCreate(['user_id' => $request->user()->id], $data + ['user_id' => $request->user()->id, 'verified_purchase' => true, 'is_approved' => false, 'moderation_status' => 'pending', 'moderated_by' => null, 'moderated_at' => null]);
        ReviewSubmitted::dispatch($review->id);

        return back()->with('success', 'Thank you. Your verified-purchase review was submitted for moderation.');
    }
}
