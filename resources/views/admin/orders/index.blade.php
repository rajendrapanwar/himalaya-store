@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="space-y-6">
    <div class="admin-page-intro"><div><p class="eyebrow">Sales & fulfilment</p><h2>Customer orders</h2><p>Review payments, update fulfilment and follow every delivery.</p></div><a href="{{ route('admin.reports.index') }}" class="admin-action admin-action-secondary">View sales reports</a></div>

    <form method="GET" class="admin-filter-bar lg:grid-cols-[minmax(280px,1fr)_220px_200px_auto_auto]" aria-label="Filter orders">
        <div class="relative"><label for="orderSearch" class="sr-only">Search orders</label><svg class="pointer-events-none absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.8" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg><input id="orderSearch" name="search" value="{{ request('search') }}" placeholder="Order number, email or phone…" class="w-full rounded-xl !pl-10 pr-4 py-3"></div>
        <div><label for="orderStatus" class="sr-only">Order status</label><select id="orderStatus" name="status" class="w-full rounded-xl px-4 py-3"><option value="">All order statuses</option>@foreach(['pending','processing','shipped','delivered','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div><label for="paymentStatus" class="sr-only">Payment status</label><select id="paymentStatus" name="payment" class="w-full rounded-xl px-4 py-3"><option value="">All payments</option>@foreach(['pending','paid','failed','refunded'] as $status)<option value="{{ $status }}" @selected(request('payment') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <button class="admin-action admin-action-primary" type="submit">Apply filters</button><a href="{{ route('admin.orders.index') }}" class="admin-action admin-action-secondary">Reset</a>
    </form>

    <section class="admin-panel">
        <header class="admin-panel-header"><div><h3 class="font-black text-brand-950">Order queue</h3><p class="mt-1 text-xs text-stone-500">{{ $orders->total() }} {{ Str::plural('order', $orders->total()) }} found</p></div><span class="hidden text-xs font-bold text-stone-500 sm:block">Newest orders first</span></header>
        <div class="overflow-x-auto"><table><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Order status</th><th>Payment</th><th class="text-right">Action</th></tr></thead><tbody>
            @forelse($orders as $order)
                @php
                    $orderTone = match($order->order_status) { 'delivered' => 'bg-brand-50 text-brand-800', 'cancelled' => 'bg-red-50 text-red-700', 'shipped' => 'bg-blue-50 text-blue-700', default => 'bg-amber-50 text-amber-800' };
                    $paymentTone = match($order->payment_status) { 'paid' => 'bg-brand-50 text-brand-800', 'failed' => 'bg-red-50 text-red-700', 'refunded' => 'bg-violet-50 text-violet-700', default => 'bg-stone-100 text-stone-600' };
                @endphp
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="font-black text-brand-900 hover:text-brand-700">{{ $order->order_number }}</a><time class="mt-1 block text-xs text-stone-500">{{ $order->created_at->format('d M Y, h:i A') }}</time></td>
                    <td><strong class="text-sm">{{ $order->address?->first_name }} {{ $order->address?->last_name }}</strong><span class="mt-1 block max-w-[220px] truncate text-xs text-stone-500">{{ $order->address?->email ?: 'No email' }}</span></td>
                    <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong><span class="mt-1 block text-[11px] uppercase text-stone-500">{{ $order->payment_method }}</span></td>
                    <td><span class="admin-badge {{ $orderTone }}">{{ ucfirst($order->order_status) }}</span></td>
                    <td><span class="admin-badge {{ $paymentTone }}">{{ ucfirst($order->payment_status) }}</span></td>
                    <td class="text-right"><a href="{{ route('admin.orders.show', $order) }}" class="admin-action admin-action-secondary !min-h-9 !px-3">View order <span aria-hidden="true">→</span></a></td>
                </tr>
            @empty<tr><td colspan="6" class="py-16 text-center"><strong class="text-lg text-brand-950">No orders match these filters</strong><p class="mt-2 text-sm text-stone-500">Clear a filter to widen the results.</p></td></tr>@endforelse
        </tbody></table></div>
        @if($orders->hasPages())<div class="border-t border-stone-200 p-4">{{ $orders->links() }}</div>@endif
    </section>
</div>
@endsection
