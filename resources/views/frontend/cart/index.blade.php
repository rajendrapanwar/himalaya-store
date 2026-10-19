@extends('layouts.app')
@section('title', 'Your cart · Pahadi Roots')

@section('content')
@php
    $itemCount = $cart->items->sum('quantity') + $cart->bundleItems->sum('quantity');
    $qualifying = max(0, (float) $subtotal - (float) $discountAmount);
    $shippingProgress = $freeShippingEligible ? 100 : min(100, $freeShippingThreshold > 0 ? ($qualifying / (float) $freeShippingThreshold) * 100 : 100);
@endphp
<header class="mb-10 flex flex-wrap items-end justify-between gap-4" data-reveal="fade">
    <div><p class="eyebrow eyebrow-line">Your selection</p><h1 class="section-title mt-4">Shopping <em>cart</em></h1></div>
    @if($itemCount)<p class="rounded-full bg-white px-4 py-2 text-sm font-bold text-stone-600 ring-1 ring-stone-200">{{ $itemCount }} {{ Str::plural('item', $itemCount) }}</p>@endif
</header>

@if($cart->items->count() || $cart->bundleItems->count())
<div class="grid gap-8 lg:grid-cols-[1fr_400px]">
    <section class="space-y-4" aria-label="Cart items">
        @foreach($cart->items as $item)
            @php($product = $item->product)
            @php($image = $product->images->firstWhere('is_main', true) ?? $product->images->first())
            <article class="surface-card group flex gap-4 p-4 transition hover:shadow-[var(--shadow-md)] sm:gap-6 sm:p-5">
                <a href="{{ route('products.show', $product->slug) }}" class="h-28 w-28 shrink-0 overflow-hidden rounded-2xl bg-parchment sm:h-36 sm:w-36">@if($image)<img src="{{ asset('storage/'.$image->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy" decoding="async" width="144" height="144">@else<div class="grid h-full place-items-center text-brand-300"><svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.5" d="m5 18 6-9 3 5 2-3 3 7H5Z"/></svg></div>@endif</a>
                <div class="flex min-w-0 flex-1 flex-col">
                    <div class="flex flex-col justify-between gap-2 sm:flex-row">
                        <div><p class="text-[10px] font-black uppercase tracking-[.16em] text-brand-600">{{ $product->category->name ?? 'Product' }}</p><a href="{{ route('products.show', $product->slug) }}"><h2 class="font-display mt-1 text-xl font-medium text-ink hover:text-brand-700 sm:text-2xl">{{ $product->name }}</h2></a><p class="mt-1 text-xs text-stone-500">₹{{ number_format((float) $item->price, 2) }} each</p></div>
                        <p class="text-lg font-black text-ink">₹{{ number_format((float) ($item->price * $item->quantity), 2) }}</p>
                    </div>
                    <div class="mt-auto flex flex-wrap items-end justify-between gap-3 pt-4">
                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">@csrf @method('PATCH')<label for="qty-{{ $item->id }}" class="sr-only">Quantity of {{ $product->name }}</label><div class="qty-stepper is-compact" data-qty-stepper><button type="button" data-qty-dec aria-label="Decrease quantity">−</button><input id="qty-{{ $item->id }}" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $product->available_stock }}" required inputmode="numeric"><button type="button" data-qty-inc aria-label="Increase quantity">+</button></div><button class="button-secondary !min-h-[42px] !px-4 !text-xs" type="submit">Update</button></form>
                        <form method="POST" action="{{ route('cart.remove', $item) }}">@csrf @method('DELETE')<button class="inline-flex items-center gap-1.5 rounded-full px-3 py-2 text-xs font-bold text-stone-500 transition hover:bg-red-50 hover:text-red-700" type="submit"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>Remove</button></form>
                    </div>
                </div>
            </article>
        @endforeach

        @foreach($cart->bundleItems as $item)
            @php($bundle = $item->bundle)
            <article class="surface-card group flex gap-4 p-4 transition hover:shadow-[var(--shadow-md)] sm:gap-6 sm:p-5">
                <div class="h-28 w-28 shrink-0 overflow-hidden rounded-2xl bg-brand-50 sm:h-36 sm:w-36">@if($bundle->image)<img src="{{ asset('storage/'.$bundle->image) }}" alt="{{ $bundle->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">@else<div class="grid h-full place-items-center text-brand-600"><svg class="h-11 w-11" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.5" d="M4 10h16v11H4V10Zm-2-5h20v5H2V5Zm10 16V5m0 0c-3 0-5-1-5-3 3 0 5 1 5 3Zm0 0c3 0 5-1 5-3-3 0-5 1-5 3Z"/></svg></div>@endif</div>
                <div class="flex min-w-0 flex-1 flex-col">
                    <div class="flex flex-col justify-between gap-2 sm:flex-row"><div><p class="text-[10px] font-black uppercase tracking-[.16em] text-saffron">Gift bundle</p><h2 class="font-display mt-1 text-xl font-medium text-ink sm:text-2xl">{{ $bundle->name }}</h2><p class="mt-1 text-xs text-stone-500">{{ $bundle->products->count() }} curated products · ₹{{ number_format((float) $bundle->price, 2) }} each</p></div><p class="text-lg font-black text-ink">₹{{ number_format((float) ($bundle->price * $item->quantity), 2) }}</p></div>
                    <div class="mt-auto flex flex-wrap items-end justify-between gap-3 pt-4"><form method="POST" action="{{ route('cart.bundles.update', $item) }}" class="flex items-center gap-2">@csrf @method('PATCH')<label for="bundle-qty-{{ $item->id }}" class="sr-only">Quantity of {{ $bundle->name }}</label><div class="qty-stepper is-compact" data-qty-stepper><button type="button" data-qty-dec aria-label="Decrease quantity">−</button><input id="bundle-qty-{{ $item->id }}" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $bundle->available_stock }}" required inputmode="numeric"><button type="button" data-qty-inc aria-label="Increase quantity">+</button></div><button class="button-secondary !min-h-[42px] !px-4 !text-xs" type="submit">Update</button></form><form method="POST" action="{{ route('cart.bundles.remove', $item) }}">@csrf @method('DELETE')<button class="inline-flex items-center gap-1.5 rounded-full px-3 py-2 text-xs font-bold text-stone-500 transition hover:bg-red-50 hover:text-red-700" type="submit">Remove</button></form></div>
                </div>
            </article>
        @endforeach
        <a href="{{ route('products.index') }}" class="button-quiet"><span aria-hidden="true">←</span> Continue shopping</a>
    </section>

    <aside>
        <div class="surface-card overflow-hidden lg:sticky lg:top-24">
            <div class="border-b border-stone-100 bg-parchment/60 p-6">
                <div class="flex items-start gap-3"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-full {{ $freeShippingEligible ? 'bg-brand-600 text-white' : 'bg-white text-saffron ring-1 ring-marigold-200' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h11v10H3V7Zm11 3h4l3 3v4h-7v-7ZM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg></span><div class="min-w-0 flex-1"><p class="text-sm font-black text-ink">{{ $freeShippingEligible ? 'You unlocked free delivery!' : 'You are close to free delivery' }}</p><p class="mt-0.5 text-xs leading-5 text-stone-600">{{ $freeShippingEligible ? 'No delivery charge will be added for a serviceable pincode.' : 'Add ₹'.number_format(ceil((float) $amountUntilFreeShipping), 0).' more to your order.' }}</p></div></div>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-stone-200" role="progressbar" aria-label="Progress toward free delivery" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ round($shippingProgress) }}"><div class="h-full rounded-full bg-gradient-to-r from-saffron to-marigold-300" style="width: {{ $shippingProgress }}%"></div></div>
            </div>
            <div class="p-6">
                <h2 class="font-display text-2xl font-medium">Order summary</h2>
                <dl class="mt-5 space-y-3 text-sm"><div class="flex justify-between gap-3 text-stone-600"><dt>Subtotal</dt><dd>₹{{ number_format((float) $subtotal, 2) }}</dd></div>@if($coupon)<div class="flex justify-between gap-3 font-bold text-brand-700"><dt>Coupon · {{ $coupon->code }}</dt><dd>−₹{{ number_format((float) $discountAmount, 2) }}</dd></div>@endif<div class="flex justify-between gap-3 text-stone-600"><dt>Delivery</dt><dd class="text-right font-bold {{ $freeShippingEligible ? 'text-brand-700' : '' }}">{{ $freeShippingEligible ? 'Free' : 'Calculated from pincode' }}</dd></div><div class="flex justify-between border-t border-stone-200 pt-4 text-lg font-black"><dt>Estimated total</dt><dd>₹{{ number_format((float) $total, 2) }}</dd></div></dl>
                @if($coupon)<div class="mt-5 rounded-2xl border border-dashed border-brand-300 bg-brand-50 p-4"><div class="flex items-start justify-between gap-3"><p class="text-sm text-brand-900"><strong class="tracking-widest">{{ $coupon->code }}</strong> is applied.<br><span class="text-xs text-brand-700">You save ₹{{ number_format((float) $discountAmount, 2) }}.</span></p><form method="POST" action="{{ route('cart.coupon.remove') }}">@csrf @method('DELETE')<button type="submit" class="text-xs font-bold text-red-700 hover:underline">Remove</button></form></div></div>@else<form method="POST" action="{{ route('cart.coupon.apply') }}" class="mt-6">@csrf<label for="couponCode" class="field-label">Have a coupon?</label><div class="flex gap-2"><input id="couponCode" name="code" value="{{ old('code') }}" autocomplete="off" maxlength="50" placeholder="Enter code" class="field-control min-w-0 uppercase tracking-widest" required><button class="button-secondary shrink-0" type="submit">Apply</button></div><p class="field-help">Eligibility is checked against the products and subtotal in this cart.</p></form>@endif
                <a href="{{ route('checkout.index') }}" class="button-primary mt-6 w-full" data-magnetic>Continue to secure checkout <span class="button-arrow" aria-hidden="true">→</span></a>
                <div class="mt-5 flex items-center justify-center gap-2 text-xs text-stone-500"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.7" d="M7 10V7a5 5 0 0 1 10 0v3m2 0H5v11h14V10Z"/></svg>Protected checkout · Cards, UPI and COD</div>
            </div>
        </div>
    </aside>
</div>
@else
<section class="relative overflow-hidden rounded-[2rem] bg-white px-6 py-20 text-center shadow-[var(--shadow-sm)] ring-1 ring-stone-200/70">
    <svg class="pointer-events-none absolute inset-x-0 bottom-0 h-32 w-full text-brand-50" viewBox="0 0 1200 120" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 120 160 40l120 60 180-90 150 80 140-50 200 80 120-40 130 40v0Z"/></svg>
    <div class="relative">
        <span class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-brand-50 text-brand-500"><svg class="h-11 w-11 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14l-1 13H6L5 8Zm3 2V6a4 4 0 0 1 8 0v4"/></svg></span>
        <h2 class="font-display mt-6 text-4xl font-medium">Your cart is waiting</h2>
        <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-stone-500">Explore the mountain pantry and add products or gift boxes you would like to receive.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3"><a href="{{ route('products.index') }}" class="button-primary" data-magnetic>Start shopping <span class="button-arrow" aria-hidden="true">→</span></a><a href="{{ route('bundles.index') }}" class="button-secondary">Browse gift boxes</a></div>
    </div>
</section>
@endif
@endsection
