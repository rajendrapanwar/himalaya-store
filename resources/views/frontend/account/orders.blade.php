@extends('layouts.app')

@section('title', 'My orders · Pahadi Roots')

@section('content')
<div class="mx-auto max-w-6xl py-8 sm:py-12">
    <div class="flex flex-wrap items-end justify-between gap-5"><div><p class="eyebrow">My account</p><h1 class="display-title mt-3 text-brand-950">Orders and deliveries</h1><p class="mt-3 text-stone-600">Review purchases, follow shipments and manage returns.</p></div><a href="{{ route('profile') }}" class="button-secondary">Account overview</a></div>
    <div class="mt-8 space-y-4">
        @forelse($orders as $order)
            @php $progress = match($order->order_status) { 'processing' => 45, 'shipped' => 72, 'delivered' => 100, 'cancelled' => 100, default => 18 }; @endphp
            <article class="surface-card overflow-hidden transition hover:border-brand-300 hover:shadow-md">
                <div class="grid gap-5 p-5 sm:p-6 lg:grid-cols-[1fr_220px_auto] lg:items-center">
                    <div><div class="flex flex-wrap items-center gap-3"><h2 class="text-lg font-black text-brand-950">{{ $order->order_number }}</h2><span class="rounded-full {{ $order->order_status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-brand-100 text-brand-800' }} px-3 py-1 text-[11px] font-black">{{ ucfirst($order->order_status) }}</span></div><p class="mt-2 text-sm text-stone-500">Placed {{ $order->created_at->format('d M Y') }} · {{ $order->address?->city ?: 'Delivery address' }}</p><div class="mt-4 max-w-xl"><div class="h-1.5 overflow-hidden rounded-full bg-stone-100"><div class="h-full rounded-full {{ $order->order_status === 'cancelled' ? 'bg-red-500' : 'bg-brand-700' }}" style="width: {{ $progress }}%"></div></div></div></div>
                    <div><span class="block text-[10px] font-black uppercase tracking-wider text-stone-400">Order total</span><strong class="mt-1 block text-xl">₹{{ number_format($order->total_amount, 2) }}</strong><span class="text-xs text-stone-500">{{ ucfirst($order->payment_status) }} · {{ ucfirst($order->payment_method) }}</span></div>
                    <div class="flex gap-2 lg:justify-end"><a href="{{ route('orders.show', $order) }}" class="button-secondary !py-2.5">Details</a><a href="{{ route('orders.track', $order) }}" class="button-primary !py-2.5">Track</a></div>
                </div>
            </article>
        @empty
            <div class="surface-card px-6 py-16 text-center"><span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-brand-100 text-brand-700"><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 8h14l-1 12H6L5 8Zm3 0a4 4 0 0 1 8 0"/></svg></span><h2 class="font-display mt-5 text-3xl font-semibold">Your pantry is ready to be filled</h2><p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">Discover grains, pulses, preserves and gift boxes sourced from the hills.</p><a href="{{ route('products.index') }}" class="button-primary mt-6">Start shopping</a></div>
        @endforelse
    </div>
    @if($orders->hasPages())<div class="surface-card mt-6 p-4">{{ $orders->links() }}</div>@endif
</div>
@endsection
