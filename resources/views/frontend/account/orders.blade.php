@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl py-12"><div class="mb-8 flex items-center justify-between"><div><p class="text-sm font-bold uppercase tracking-widest text-emerald-700">My account</p><h1 class="mt-2 text-4xl font-black">My orders</h1></div><a href="{{ route('profile') }}" class="font-bold text-emerald-700">Profile</a></div>
@forelse($orders as $order)
<a href="{{ route('orders.show', $order) }}" class="mb-4 block rounded-2xl border border-stone-200 bg-white p-6 shadow-sm transition hover:border-emerald-300 hover:shadow-md"><div class="flex flex-wrap items-center justify-between gap-3"><div><p class="font-black">{{ $order->order_number }}</p><p class="mt-1 text-sm text-stone-500">Placed {{ $order->created_at->format('d M Y') }}</p></div><div class="text-right"><p class="font-black">₹{{ number_format($order->total_amount, 2) }}</p><span class="mt-1 inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">{{ ucfirst($order->order_status) }}</span></div></div></a>
@empty <div class="rounded-3xl border border-dashed border-stone-300 p-12 text-center"><p class="text-lg font-bold">You have not placed an order yet.</p><a href="{{ route('products.index') }}" class="mt-5 inline-block font-bold text-emerald-700">Start shopping</a></div>@endforelse
{{ $orders->links() }}</div>
@endsection
