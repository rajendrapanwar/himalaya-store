@extends('layouts.app')

@php
    $mainImage = $product->images->firstWhere('is_main', true) ?? $product->images->first();
    $displayPrice = $product->sale_price ?? $product->price;
    $reviewCount = $product->reviews->count();
    $averageRating = $reviewCount ? round((float) $product->reviews->avg('rating'), 1) : 0;
    $productMetaDescription = trim($product->meta_description ?: $product->short_description ?: 'Discover '.$product->name.', sourced from '.($product->origin_region ?: 'the Himalayas').'.');
    if (Str::length($productMetaDescription) < 90) {
        $productMetaDescription .= ' View origin, ingredients, pack details, availability and delivery information.';
    }
@endphp

@section('title', $product->seo_title ?: $product->name.' | Pahadi Roots')
@section('meta_description', Str::limit($productMetaDescription, 155, ''))
@section('canonical', route('products.show', $product->slug))
@section('og_type', 'product')
@section('og_image', $mainImage ? asset('storage/'.$mainImage->image) : asset('assets/img/logo.png'))
@section('og_image_alt', $product->name.' from Pahadi Roots')

@push('head')
<meta property="product:price:amount" content="{{ number_format((float) $displayPrice, 2, '.', '') }}"><meta property="product:price:currency" content="INR"><meta property="product:availability" content="{{ $product->available_stock > 0 ? 'in stock' : 'out of stock' }}"><meta property="product:retailer_item_id" content="{{ $product->sku ?: $product->id }}">
<x-structured-data :data="[
    '@context' => 'https://schema.org',
    '@graph' => [[
        '@type' => 'Product',
        '@id' => route('products.show', $product->slug).'#product',
        'url' => route('products.show', $product->slug),
        'name' => $product->name,
        'image' => $product->images->sortByDesc('is_main')->map(fn ($image) => asset('storage/'.$image->image))->whenEmpty(fn ($images) => $images->push(asset(config('seo.default_social_image'))))->values()->all(),
        'description' => $productMetaDescription,
        'sku' => $product->sku,
        'category' => $product->category?->name,
        'brand' => ['@type' => 'Brand', 'name' => 'Pahadi Roots'],
        'additionalProperty' => array_values(array_filter([
            $product->origin_region ? ['@type' => 'PropertyValue', 'name' => 'Origin', 'value' => $product->origin_region] : null,
            $product->producer_name ? ['@type' => 'PropertyValue', 'name' => 'Producer', 'value' => $product->producer_name] : null,
            $product->weight ? ['@type' => 'PropertyValue', 'name' => 'Pack weight', 'value' => number_format((float) $product->weight, 0).' g'] : null,
        ])),
        'offers' => array_filter([
            '@type' => 'Offer',
            'url' => route('products.show', $product->slug),
            'priceCurrency' => 'INR',
            'price' => number_format((float) $displayPrice, 2, '.', ''),
            'availability' => $product->available_stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => ['@id' => route('home').'#organization'],
            'priceSpecification' => $product->sale_price ? [
                '@type' => 'UnitPriceSpecification',
                'priceType' => 'https://schema.org/StrikethroughPrice',
                'price' => number_format((float) $product->price, 2, '.', ''),
                'priceCurrency' => 'INR',
            ] : null,
        ]),
    ] + ($reviewCount ? [
        'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => $averageRating, 'reviewCount' => $reviewCount, 'bestRating' => 5, 'worstRating' => 1],
        'review' => $product->reviews->map(fn ($review) => array_filter([
            '@type' => 'Review',
            'author' => ['@type' => 'Person', 'name' => $review->user->name],
            'datePublished' => $review->created_at->toDateString(),
            'name' => $review->title,
            'reviewBody' => $review->body,
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $review->rating, 'bestRating' => 5, 'worstRating' => 1],
        ]))->values()->all(),
    ] : []), [
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_values(array_filter([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => route('products.index')],
            $product->category ? ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category->name, 'item' => route('products.index', ['category' => $product->category->slug])] : null,
            ['@type' => 'ListItem', 'position' => $product->category ? 4 : 3, 'name' => $product->name, 'item' => route('products.show', $product->slug)],
        ])),
    ]],
]" />
@endpush

@section('content')
@php
    $galleryImages = $product->images->sortByDesc('is_main')->values();
    $inStock = $product->available_stock > 0;
    $packSize = $product->weight ? ((float) $product->weight >= 1000 ? rtrim(rtrim(number_format((float) $product->weight / 1000, 2), '0'), '.').' kg' : number_format((float) $product->weight, 0).' g') : null;
    $discountPercent = $product->sale_price && (float) $product->price > 0 ? (int) round((1 - (float) $product->sale_price / (float) $product->price) * 100) : 0;
    $ratingBreakdown = collect(range(5, 1))->mapWithKeys(fn ($stars) => [$stars => $product->reviews->where('rating', $stars)->count()]);
@endphp
<nav class="mb-8 flex flex-wrap items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="link-underline hover:text-brand-700">Home</a><span aria-hidden="true" class="text-stone-300">/</span><a href="{{ route('products.index') }}" class="link-underline hover:text-brand-700">Shop</a>@if($product->category)<span aria-hidden="true" class="text-stone-300">/</span><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="link-underline hover:text-brand-700">{{ $product->category->name }}</a>@endif<span aria-hidden="true" class="text-stone-300">/</span><span class="text-stone-800">{{ $product->name }}</span></nav>

<section class="grid gap-10 lg:grid-cols-[1.08fr_.92fr] lg:gap-16">
    <div class="lg:sticky lg:top-28 lg:self-start" data-reveal="fade">
        <div class="zoom-frame relative overflow-hidden rounded-[2rem] bg-parchment shadow-[var(--shadow-sm)]" data-zoom>
            @if($mainImage)
                <img id="mainProductImage" src="{{ asset('storage/'.$mainImage->image) }}" data-image-fallback alt="{{ $product->name }} from {{ $product->origin_region ?: 'the Himalayan region' }}" class="aspect-square h-auto w-full object-cover" width="760" height="760" fetchpriority="high" decoding="async">
                <button type="button" class="absolute bottom-4 right-4 grid h-11 w-11 place-items-center rounded-full bg-white/90 text-brand-950 shadow-lg backdrop-blur transition hover:scale-110" data-lightbox-open aria-label="View {{ $product->name }} full screen"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg></button>
            @else
                <div class="flex aspect-square flex-col items-center justify-center gap-4 px-8 text-center text-stone-500"><svg class="h-20 w-20 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/></svg><p class="text-sm font-bold uppercase tracking-[.15em]">Product photography coming soon</p></div>
            @endif
            @if($product->sale_price)<span class="badge badge-sale pointer-events-none absolute left-5 top-5 !px-4 !py-2 !text-[11px]">Save ₹{{ number_format((float) ($product->price - $product->sale_price), 2) }}</span>@endif
        </div>
        @if($galleryImages->count() > 1)
            <div class="mt-4 grid grid-cols-5 gap-3" role="list" aria-label="Product images">@foreach($galleryImages as $image)<button type="button" class="thumb-button" role="listitem" data-product-thumbnail data-src="{{ asset('storage/'.$image->image) }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Show product image {{ $loop->iteration }}"><img src="{{ asset('storage/'.$image->image) }}" data-image-fallback alt="" class="aspect-square w-full object-cover" loading="lazy" decoding="async" width="160" height="160"></button>@endforeach</div>
        @endif
    </div>

    <div>
        <div class="flex items-start justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2"><a href="{{ $product->category ? route('products.index', ['category' => $product->category->slug]) : route('products.index') }}" class="rounded-full bg-brand-100 px-3.5 py-1.5 text-[11px] font-black uppercase tracking-wider text-brand-800 transition hover:bg-brand-200">{{ $product->category->name ?? 'Mountain produce' }}</a>@if($product->is_featured)<span class="rounded-full border border-saffron/40 bg-marigold-100 px-3.5 py-1.5 text-[11px] font-black uppercase tracking-wider text-clay-600">★ Featured</span>@endif</div>
            <div class="relative shrink-0"><button type="button" class="icon-button border border-stone-200 bg-white" data-share data-share-title="{{ $product->name }}" data-share-text="{{ Str::limit($product->short_description ?: 'Discover '.$product->name.' from Pahadi Roots.', 120) }}" data-share-url="{{ route('products.show', $product->slug) }}" aria-label="Share {{ $product->name }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.8"/><circle cx="6" cy="12" r="2.5" stroke-width="1.8"/><circle cx="18" cy="19" r="2.5" stroke-width="1.8"/><path stroke-width="1.8" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button><span data-share-feedback role="status" class="pointer-events-none absolute right-0 top-12 hidden whitespace-nowrap rounded-full bg-brand-950 px-3 py-2 text-[11px] font-bold text-white shadow-lg">Link copied</span></div>
        </div>
        <h1 class="font-display mt-5 text-[clamp(2.5rem,5vw,4rem)] font-medium leading-[1] tracking-[-.03em] text-ink">{{ $product->name }}</h1>
        @if($product->origin_region)<p class="mt-3 flex items-center gap-2 text-sm font-semibold text-stone-500"><svg class="h-4 w-4 text-saffron" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>Sourced from {{ $product->origin_region }}</p>@endif
        @if($reviewCount)
            <a href="#customer-reviews" class="mt-4 inline-flex items-center gap-2 text-sm text-stone-600 hover:text-brand-800"><span class="rating-stars !text-base" style="--rating: {{ $averageRating }}" aria-hidden="true"></span><strong class="text-ink">{{ number_format($averageRating, 1) }}</strong><span class="link-underline">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span></a>
        @else
            <p class="mt-4 text-sm text-stone-500">Not reviewed yet</p>
        @endif
        @if($product->short_description)
            <p class="lead mt-6">{{ $product->short_description }}</p>
        @endif

        <div class="mt-8 rounded-[1.75rem] bg-white p-6 shadow-[var(--shadow-sm)] ring-1 ring-stone-200/70 sm:p-7" data-buy-box>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <p class="flex flex-wrap items-baseline gap-3"><span class="text-4xl font-black tracking-tight text-ink">₹{{ number_format((float) $displayPrice, 2) }}</span>@if($product->sale_price)<span class="text-lg text-stone-400 line-through">₹{{ number_format((float) $product->price, 2) }}</span><span class="badge badge-sale">−{{ $discountPercent }}%</span>@endif</p>
                @if($packSize)<span class="rounded-full bg-parchment px-3 py-1.5 text-xs font-bold text-stone-600">{{ $packSize }} pack</span>@endif
            </div>
            <p class="mt-1 text-xs text-stone-500">Inclusive of all taxes · Free delivery over ₹{{ number_format((float) config('commerce.free_shipping_threshold'), 0) }}</p>

            <div class="mt-5 flex items-center gap-2 text-sm">@if($inStock)<span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-60"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-brand-500"></span></span><strong>In stock</strong><span class="text-stone-500">· {{ $product->available_stock <= 5 ? 'Only '.$product->available_stock.' left' : $product->available_stock.' available' }}</span>@else<span class="h-2.5 w-2.5 rounded-full bg-red-500"></span><strong class="text-red-700">Currently unavailable</strong>@endif</div>

            <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-5 flex flex-wrap gap-3" data-add-to-cart data-open-drawer>@csrf
                <div class="qty-stepper" data-qty-stepper><button type="button" data-qty-dec aria-label="Decrease quantity">−</button><label for="quantity" class="sr-only">Quantity</label><input id="quantity" type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->available_stock) }}" required inputmode="numeric"><button type="button" data-qty-inc aria-label="Increase quantity">+</button></div>
                <button type="submit" class="button-primary min-h-[52px] flex-1" @disabled(! $inStock)><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 8h14l-1 13H6L5 8Zm3 2V6a4 4 0 0 1 8 0v4"/></svg>Add to cart</button>
            </form>
            <div class="mt-3">
                @auth<form method="POST" action="{{ $wishlistSaved ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" data-wishlist-form data-product-id="{{ $product->id }}" data-add-url="{{ route('wishlist.store', $product) }}" data-remove-url="{{ route('wishlist.destroy', $product) }}" data-product-name="{{ $product->name }}">@csrf @if($wishlistSaved)@method('DELETE')@endif<button type="submit" data-wishlist-button aria-pressed="{{ $wishlistSaved ? 'true' : 'false' }}" class="button-secondary w-full disabled:cursor-wait disabled:opacity-70"><svg data-wishlist-icon class="h-5 w-5 text-red-600" fill="{{ $wishlistSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg><span data-wishlist-label>{{ $wishlistSaved ? 'Saved to wishlist' : 'Save to wishlist' }}</span></button></form>@else<a href="{{ route('login') }}" class="button-secondary w-full"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>Sign in to save</a>@endauth
            </div>

            <form method="POST" action="{{ route('pincode.check') }}" class="mt-6 border-t border-stone-100 pt-5" data-pincode-form>@csrf<label for="deliveryPincode" class="field-label flex items-center gap-2"><svg class="h-4 w-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h11v10H3V7Zm11 3h4l3 3v4h-7v-7ZM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg>Check delivery to your pincode</label><div class="flex gap-2"><input id="deliveryPincode" name="pincode" inputmode="numeric" autocomplete="postal-code" pattern="[1-9][0-9]{5}" maxlength="6" placeholder="6-digit pincode" class="field-control min-w-0" required><button class="button-secondary shrink-0" type="submit" data-pincode-button>Check</button></div><div data-pincode-result role="status" aria-live="polite" class="mt-3 hidden rounded-2xl border p-4 text-sm"></div></form>
        </div>

        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/70"><dt class="text-[10px] font-black uppercase tracking-[.16em] text-stone-500">Origin</dt><dd class="mt-1.5 font-display text-lg text-ink">{{ $product->origin_region ?: 'Himalayan region' }}</dd></div>
            <div class="rounded-2xl bg-white p-5 ring-1 ring-stone-200/70"><dt class="text-[10px] font-black uppercase tracking-[.16em] text-stone-500">Producer</dt><dd class="mt-1.5 font-display text-lg text-ink">{{ $product->producer_name ?: 'Pahadi Roots partner' }}</dd></div>
        </dl>

        <ul class="mt-6 grid gap-3 text-sm sm:grid-cols-3" aria-label="Ordering information">
            <li class="flex items-start gap-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10V7a5 5 0 0 1 10 0v3m2 0H5v11h14V10Z"/></svg></span><span><strong class="block text-xs text-ink">Secure checkout</strong><span class="text-xs text-stone-500">Cards, UPI & COD</span></span></li>
            <li class="flex items-start gap-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 0 1 14-5.3L20 9M20 4v5h-5"/></svg></span><span><strong class="block text-xs text-ink">30-day returns</strong><a href="{{ route('returns.policy') }}" class="text-xs text-stone-500 underline">Read policy</a></span></li>
            <li class="flex items-start gap-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg></span><span><strong class="block text-xs text-ink">Human care</strong><a href="{{ route('contact') }}" class="text-xs text-stone-500 underline">Ask our team</a></span></li>
        </ul>
    </div>
</section>

<section class="section-space pb-0">
    <div class="grid gap-6 lg:grid-cols-[1.35fr_.65fr]">
        <article class="surface-card p-7 sm:p-10" data-reveal><p class="eyebrow eyebrow-line">Product story</p><h2 class="section-title mt-4 !text-[clamp(2rem,3.4vw,2.9rem)]">What makes it <em>special</em></h2><div class="mt-6 space-y-4 text-base leading-8 text-stone-600">{!! nl2br(e($product->description ?: $product->short_description ?: 'More sourcing information will be added soon.')) !!}</div>@if($product->ingredients)<div class="mt-8 rounded-2xl bg-parchment p-5"><h3 class="text-xs font-black uppercase tracking-[.16em] text-brand-700">Ingredients</h3><p class="mt-2 text-sm leading-7 text-stone-700">{{ $product->ingredients }}</p></div>@endif</article>
        <aside class="surface-card p-7 sm:p-8" data-reveal><p class="eyebrow eyebrow-line">Batch information</p><dl class="mt-5 divide-y divide-stone-100 text-sm"><div class="flex justify-between gap-5 py-3.5"><dt class="text-stone-500">SKU</dt><dd class="text-right font-bold">{{ $product->sku ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3.5"><dt class="text-stone-500">Batch</dt><dd class="text-right font-bold">{{ $product->batch_number ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3.5"><dt class="text-stone-500">Harvested</dt><dd class="text-right font-bold">{{ $product->harvested_at?->format('d M Y') ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3.5"><dt class="text-stone-500">Producer location</dt><dd class="text-right font-bold">{{ $product->producer_location ?: $product->origin_region ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3.5"><dt class="text-stone-500">Pack weight</dt><dd class="text-right font-bold">{{ $packSize ?: 'Not specified' }}</dd></div></dl></aside>
    </div>
</section>

<section id="customer-reviews" class="section-space pb-0 scroll-mt-32">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between" data-reveal><div><p class="eyebrow eyebrow-line">Verified customer feedback</p><h2 class="section-title mt-4">Customer <em>reviews</em></h2></div></div>
    <div class="mt-10 grid gap-6 lg:grid-cols-[.7fr_1.3fr]">
        <aside class="space-y-5 lg:sticky lg:top-28 lg:self-start">
            @if($reviewCount)
                <div class="surface-card p-7" data-reveal>
                    <div class="flex items-end gap-4"><p class="font-display text-6xl font-medium leading-none text-ink">{{ number_format($averageRating, 1) }}</p><div class="pb-1"><span class="rating-stars !text-lg" style="--rating: {{ $averageRating }}" aria-hidden="true"></span><p class="text-xs text-stone-500">{{ $reviewCount }} verified {{ Str::plural('review', $reviewCount) }}</p></div></div>
                    <ul class="mt-6 space-y-2" aria-label="Rating breakdown">
                        @foreach($ratingBreakdown as $stars => $count)
                            <li class="flex items-center gap-3 text-xs"><span class="w-8 font-bold text-stone-600">{{ $stars }} ★</span><span class="h-2 flex-1 overflow-hidden rounded-full bg-stone-100"><span class="block h-full rounded-full bg-saffron" style="width: {{ $reviewCount ? round($count / $reviewCount * 100) : 0 }}%"></span></span><span class="w-6 text-right text-stone-500">{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="surface-card p-7" data-reveal><h3 class="font-display text-2xl font-medium">Share your experience</h3>@auth @if($canReview)<p class="mt-2 text-sm leading-6 text-stone-500">Your review will appear after moderation.</p><form method="POST" action="{{ route('reviews.store', $product->id) }}" class="mt-5 space-y-4">@csrf<div><label for="reviewRating" class="field-label">Rating</label><select id="reviewRating" name="rating" class="field-control catalogue-select" required>@foreach([5=>'Excellent — 5 stars',4=>'Very good — 4 stars',3=>'Good — 3 stars',2=>'Fair — 2 stars',1=>'Poor — 1 star'] as $value=>$label)<option value="{{ $value }}" @selected((int)old('rating',5)===$value)>{{ $label }}</option>@endforeach</select></div><div><label for="reviewTitle" class="field-label">Review title <span class="font-normal text-stone-500">(optional)</span></label><input id="reviewTitle" name="title" value="{{ old('title') }}" maxlength="150" class="field-control"></div><div><label for="reviewBody" class="field-label">Your review</label><textarea id="reviewBody" name="body" rows="5" minlength="10" maxlength="2000" class="field-control" required>{{ old('body') }}</textarea><p class="field-help">At least 10 characters. Please focus on the product.</p></div><button class="button-primary w-full" type="submit">Submit for approval</button></form>@else<p class="mt-3 text-sm leading-6 text-stone-600">Reviews are available after a verified purchase of this product.</p><a href="{{ route('orders') }}" class="button-secondary mt-5">View your orders</a>@endif @else<p class="mt-3 text-sm leading-6 text-stone-600">Sign in after receiving your order to leave a verified review.</p><a href="{{ route('login') }}" class="button-primary mt-5">Sign in</a>@endauth</div>
        </aside>
        <div class="space-y-4" data-reveal-stagger>
            @forelse($product->reviews as $review)
                <article class="surface-card p-7" data-reveal>
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-full bg-brand-900 text-sm font-black text-marigold-300" aria-hidden="true">{{ Str::upper(Str::substr($review->user->name, 0, 1)) }}</span><div><p class="text-sm font-bold text-ink">{{ $review->user->name }}</p><p class="text-xs text-stone-500">{{ $review->created_at->format('d M Y') }}</p></div></div>
                        @if($review->verified_purchase)<span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-3 py-1 text-[11px] font-black text-brand-700"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>Verified purchase</span>@endif
                    </div>
                    <p class="rating-stars mt-5" style="--rating: {{ $review->rating }}" aria-label="{{ $review->rating }} out of 5 stars"></p>
                    <h3 class="font-display mt-2 text-xl font-medium text-ink">{{ $review->title ?: 'Customer review' }}</h3>
                    <p class="mt-3 leading-7 text-stone-600">{{ $review->body }}</p>
                </article>
            @empty
                <div class="surface-card p-12 text-center"><p class="text-4xl text-saffron" aria-hidden="true">☆</p><h3 class="font-display mt-3 text-2xl font-medium">No approved reviews yet</h3><p class="mt-2 text-sm text-stone-500">Verified buyers can be the first to share their experience.</p></div>
            @endforelse
        </div>
    </div>
</section>

@if($relatedProducts->isNotEmpty())<section class="section-space pb-0"><div class="flex items-end justify-between gap-4" data-reveal><div><p class="eyebrow eyebrow-line">Continue exploring</p><h2 class="section-title mt-4">You may <em>also like</em></h2></div><a href="{{ route('products.index') }}" class="button-secondary hidden sm:inline-flex">View all <span class="button-arrow" aria-hidden="true">→</span></a></div><div class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4" data-reveal-stagger>@foreach($relatedProducts as $related)<div data-reveal><x-product-card :product="$related" /></div>@endforeach</div></section>@endif

{{-- Sticky purchase bar once the main buy box has scrolled away --}}
<div class="sticky-buy" data-sticky-buy aria-label="Quick add to cart">
    <div class="site-shell flex items-center gap-4 py-3">
        @if($mainImage)<img src="{{ asset('storage/'.$mainImage->image) }}" alt="" class="hidden h-12 w-12 rounded-xl object-cover sm:block" width="48" height="48" loading="lazy">@endif
        <div class="min-w-0 flex-1"><p class="truncate font-display text-lg leading-tight text-ink">{{ $product->name }}</p><p class="text-sm font-black text-ink">₹{{ number_format((float) $displayPrice, 2) }}@if($product->sale_price)<span class="ml-2 text-xs font-normal text-stone-400 line-through">₹{{ number_format((float) $product->price, 2) }}</span>@endif</p></div>
        @if($inStock)<form method="POST" action="{{ route('cart.add', $product) }}" data-add-to-cart data-open-drawer>@csrf<input type="hidden" name="quantity" value="1"><button type="submit" class="button-primary">Add to cart</button></form>@else<span class="text-sm font-bold text-red-700">Sold out</span>@endif
    </div>
</div>

@if($galleryImages->isNotEmpty())
<dialog class="lightbox" data-lightbox aria-label="{{ $product->name }} images">
    <div class="relative flex h-full w-full items-center justify-center p-4 sm:p-12">
        <img src="" alt="" class="max-h-full max-w-full rounded-2xl object-contain shadow-2xl">
        <button type="button" class="absolute right-4 top-4 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-brand-950" data-lightbox-close aria-label="Close image viewer"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18"/></svg></button>
        @if($galleryImages->count() > 1)
            <button type="button" class="absolute left-4 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-brand-950" data-lightbox-previous aria-label="Previous image"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg></button>
            <button type="button" class="absolute right-4 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-brand-950" data-lightbox-next aria-label="Next image"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 6 6 6-6 6"/></svg></button>
        @endif
    </div>
</dialog>
@endif
@endsection
