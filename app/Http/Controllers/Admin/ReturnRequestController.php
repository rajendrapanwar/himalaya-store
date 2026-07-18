<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest as CustomerReturn;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $returns = CustomerReturn::with(['order', 'user'])->when($request->status, fn ($query, $status) => $query->where('status', $status))->latest()->paginate(20)->withQueryString();

        return view('admin.returns.index', compact('returns'));
    }

    public function update(Request $request, CustomerReturn $returnRequest)
    {
        $data = $request->validate(['status' => 'required|in:approved,rejected,completed', 'admin_note' => 'required|string|max:1500']);
        $allowed = ['requested' => ['approved', 'rejected'], 'approved' => ['completed'], 'rejected' => [], 'completed' => []];
        abort_unless(in_array($data['status'], $allowed[$returnRequest->status] ?? [], true), 422, 'That return transition is not allowed.');
        if ($data['status'] === 'completed') {
            abort_unless($returnRequest->order->payment_status === 'refunded', 422, 'Refund the order before completing the return.');
        }
        $timestamps = match ($data['status']) {
            'approved' => ['approved_at' => now()],
            'rejected' => ['rejected_at' => now()],
            'completed' => ['completed_at' => now()],
        };
        $returnRequest->update($data + $timestamps + ['resolved_by' => $request->user()->id]);

        return back()->with('success', 'Return request updated.');
    }
}
