<?php

namespace App\Http\Controllers\Frontend;

use App\Events\ReturnRequestSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReturnRequestController extends Controller
{
    public function store(Request $request, Order $order)
    {
        Gate::authorize('view', $order);
        $order->loadMissing('returnRequest');
        abort_unless($order->user_id === $request->user()->id && $order->isReturnEligible(), 422, 'This order is not eligible for a return request.');
        $data = $request->validate(['reason' => 'required|in:damaged,incorrect,quality,changed_mind,other', 'details' => 'required|string|min:20|max:2000']);
        $returnRequest = $order->returnRequest()->create($data + ['user_id' => $request->user()->id, 'status' => 'requested']);
        ReturnRequestSubmitted::dispatch($returnRequest->id);

        return back()->with('success', 'Your return request has been submitted. Our team will review it shortly.');
    }
}
