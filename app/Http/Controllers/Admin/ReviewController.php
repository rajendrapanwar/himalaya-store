<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['product', 'user', 'moderator'])->when($request->status, fn ($query, $status) => $query->where('moderation_status', $status))->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate(['moderation_status' => 'required|in:pending,approved,rejected', 'moderation_note' => 'nullable|string|max:1000']);
        $review->update($data + ['is_approved' => $data['moderation_status'] === 'approved', 'moderated_by' => $request->user()->id, 'moderated_at' => now()]);

        return back()->with('success', 'Review moderation updated.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
