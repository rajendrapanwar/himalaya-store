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
<nav class="mb-6 flex flex-wrap items-center gap-2 text-xs text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-brand-700">Home</a><span aria-hidden="true">/</span><a href="{{ route('products.index') }}" class="hover:text-brand-700">Shop</a>@if($product->category)<span aria-hidden="true">/</span><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-brand-700">{{ $product->category->name }}</a>@endif<span aria-hidden="true">/</span><span class="font-semibold text-stone-800">{{ $product->name }}</span></nav>

<section class="grid gap-9 lg:grid-cols-2 lg:gap-14">
    <div>
        <div class="relative overflow-hidden rounded-2xl border border-stone-200 bg-[#f0ede5]">
            @if($mainImage)
                <img id="mainProductImage" src="{{ asset('storage/'.$mainImage->image) }}" data-image-fallback alt="{{ $product->name }} from {{ $product->origin_region ?: 'the Himalayan region' }}" class="aspect-square h-auto w-full object-cover" width="760" height="760" fetchpriority="high" decoding="async">
            @else
                <div class="flex aspect-square flex-col items-center justify-center gap-4 px-8 text-center text-stone-500"><svg class="h-20 w-20 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/></svg><p class="text-sm font-bold uppercase tracking-[.15em]">Product photography coming soon</p></div>
            @endif
            @if($product->sale_price)<span class="absolute left-4 top-4 rounded-lg bg-saffron px-3 py-1.5 text-xs font-black text-white">Save ₹{{ number_format((float) ($product->price - $product->sale_price), 2) }}</span>@endif
        </div>
        @if($product->images->count() > 1)
            <div class="mt-3 grid grid-cols-5 gap-2" role="list" aria-label="Product images">@foreach($product->images->sortByDesc('is_main') as $image)<button type="button" class="overflow-hidden rounded-xl border-2 {{ $image->id === $mainImage?->id ? 'border-brand-600' : 'border-transparent' }} bg-stone-100" data-product-thumbnail data-src="{{ asset('storage/'.$image->image) }}" aria-label="Show product image {{ $loop->iteration }}"><img src="{{ asset('storage/'.$image->image) }}" data-image-fallback alt="" class="aspect-square w-full object-cover" loading="lazy" decoding="async" width="160" height="160"></button>@endforeach</div>
        @endif
    </div>

    <div class="self-start lg:sticky lg:top-32">
        <div class="flex items-start justify-between gap-4"><div class="flex flex-wrap items-center gap-2"><span class="rounded-full bg-brand-100 px-3 py-1 text-[11px] font-black uppercase tracking-wider text-brand-800">{{ $product->category->name ?? 'Mountain produce' }}</span>@if($product->is_featured)<span class="rounded-full border border-saffron/40 bg-amber-50 px-3 py-1 text-[11px] font-black uppercase tracking-wider text-amber-800">Featured</span>@endif</div><div class="relative shrink-0"><button type="button" class="icon-button cursor-pointer border border-stone-200 bg-white" data-share data-share-title="{{ $product->name }}" data-share-text="{{ Str::limit($product->short_description ?: 'Discover '.$product->name.' from Pahadi Roots.', 120) }}" data-share-url="{{ route('products.show', $product->slug) }}" aria-label="Share {{ $product->name }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.8"/><circle cx="6" cy="12" r="2.5" stroke-width="1.8"/><circle cx="18" cy="19" r="2.5" stroke-width="1.8"/><path stroke-width="1.8" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button><span data-share-feedback role="status" class="pointer-events-none absolute right-0 top-12 hidden whitespace-nowrap rounded-lg bg-brand-950 px-3 py-2 text-[11px] font-bold text-white shadow-lg">Link copied</span></div></div>
        <h1 class="font-display mt-4 text-4xl font-semibold leading-[1.05] tracking-tight text-stone-950 sm:text-5xl">{{ $product->name }}</h1>
        @if($reviewCount)
            <a href="#customer-reviews" class="mt-4 inline-flex items-center gap-2 text-sm text-stone-600"><span class="text-saffron" aria-hidden="true">★★★★★</span><strong class="text-stone-900">{{ number_format($averageRating, 1) }}</strong><span>{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span></a>
        @else
            <p class="mt-4 text-sm text-stone-500">Not reviewed yet</p>
        @endif
        @if($product->short_description)
            <p class="mt-5 text-base leading-8 text-stone-600">{{ $product->short_description }}</p>
        @endif

        <div class="mt-6 flex flex-wrap items-baseline gap-3 border-y border-stone-200 py-5">
            <span class="text-3xl font-black text-stone-950">₹{{ number_format((float) $displayPrice, 2) }}</span>
            @if($product->sale_price)
                <span class="text-base text-stone-400 line-through">₹{{ number_format((float) $product->price, 2) }}</span>
            @endif
            @if($product->weight)
                <span class="text-xs text-stone-500">{{ (float) $product->weight >= 1000 ? rtrim(rtrim(number_format((float) $product->weight / 1000, 2), '0'), '.').' kg' : number_format((float) $product->weight, 0).' g' }} pack</span>
            @endif
        </div>

        <div class="mt-5 flex items-center gap-2 text-sm"><span class="h-2.5 w-2.5 rounded-full {{ $product->available_stock > 0 ? 'bg-brand-500' : 'bg-red-500' }}"></span>@if($product->available_stock > 0)<strong>In stock</strong><span class="text-stone-500">· {{ $product->available_stock }} available</span>@else<strong class="text-red-700">Currently unavailable</strong>@endif</div>

        <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-6 flex gap-3">@csrf<div><label for="quantity" class="sr-only">Quantity</label><input id="quantity" type="number" name="quantity" value="1" min="1" max="{{ $product->available_stock }}" class="w-24 rounded-xl px-3 text-center" required></div><button type="submit" class="button-primary flex-1 cursor-pointer disabled:cursor-not-allowed" @disabled($product->available_stock < 1)>Add to cart</button></form>
        <div class="mt-3">
            @auth<form method="POST" action="{{ $wishlistSaved ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" data-wishlist-form data-product-id="{{ $product->id }}" data-add-url="{{ route('wishlist.store', $product) }}" data-remove-url="{{ route('wishlist.destroy', $product) }}" data-product-name="{{ $product->name }}">@csrf @if($wishlistSaved)@method('DELETE')@endif<button type="submit" data-wishlist-button aria-pressed="{{ $wishlistSaved ? 'true' : 'false' }}" class="button-secondary w-full cursor-pointer disabled:cursor-wait disabled:opacity-70"><span data-wishlist-icon aria-hidden="true">{{ $wishlistSaved ? '♥' : '♡' }}</span><span data-wishlist-label>{{ $wishlistSaved ? 'Remove from wishlist' : 'Save to wishlist' }}</span></button></form>@else<a href="{{ route('login') }}" class="button-secondary w-full cursor-pointer"><span aria-hidden="true">♡</span>Sign in to save</a>@endauth
        </div>

        <form method="POST" action="{{ route('pincode.check') }}" class="mt-6 rounded-xl bg-white p-4 ring-1 ring-stone-200" data-pincode-form>@csrf<label for="deliveryPincode" class="field-label">Check delivery availability</label><div class="flex gap-2"><input id="deliveryPincode" name="pincode" inputmode="numeric" autocomplete="postal-code" pattern="[1-9][0-9]{5}" maxlength="6" placeholder="6-digit pincode" class="field-control min-w-0" required><button class="button-secondary shrink-0" type="submit" data-pincode-button>Check</button></div><div data-pincode-result role="status" aria-live="polite" class="mt-3 hidden rounded-lg border p-4 text-sm"></div></form>

        <dl class="mt-6 grid grid-cols-2 gap-3 text-sm"><div class="rounded-xl bg-white p-4 ring-1 ring-stone-200"><dt class="text-xs font-bold uppercase tracking-wider text-stone-500">Origin</dt><dd class="mt-1 font-extrabold">{{ $product->origin_region ?: 'Himalayan region' }}</dd></div><div class="rounded-xl bg-white p-4 ring-1 ring-stone-200"><dt class="text-xs font-bold uppercase tracking-wider text-stone-500">Producer</dt><dd class="mt-1 font-extrabold">{{ $product->producer_name ?: 'Pahadi Roots partner' }}</dd></div></dl>
    </div>
</section>

<section class="mt-8 grid gap-4 border-y border-stone-200 py-7 sm:grid-cols-3" aria-label="Ordering information">
    <div><h2 class="text-sm font-extrabold text-brand-950">Clear product information</h2><p class="mt-2 text-xs leading-6 text-stone-600">Available origin, producer, ingredient and pack details are published on this page.</p></div>
    <div><h2 class="text-sm font-extrabold text-brand-950">Delivery from Dehradun</h2><p class="mt-2 text-xs leading-6 text-stone-600">Check your pincode above or read our <a href="{{ route('shipping.policy') }}" class="font-bold text-brand-700 underline">shipping policy</a>.</p></div>
    <div><h2 class="text-sm font-extrabold text-brand-950">Human customer care</h2><p class="mt-2 text-xs leading-6 text-stone-600">Questions before ordering? <a href="{{ route('contact') }}" class="font-bold text-brand-700 underline">Contact our Dehradun team</a>.</p></div>
</section>

<section class="section-space pb-0">
    <div class="grid gap-6 lg:grid-cols-[1.35fr_.65fr]">
        <article class="surface-card p-6 sm:p-8"><p class="eyebrow">Product story</p><h2 class="font-display mt-3 text-3xl font-semibold">What makes it special</h2><div class="mt-5 space-y-4 leading-8 text-stone-600">{!! nl2br(e($product->description ?: $product->short_description ?: 'More sourcing information will be added soon.')) !!}</div>@if($product->ingredients)<div class="mt-7 border-t border-stone-200 pt-5"><h3 class="font-extrabold">Ingredients</h3><p class="mt-2 text-sm leading-7 text-stone-600">{{ $product->ingredients }}</p></div>@endif</article>
        <aside class="surface-card p-6 sm:p-8"><p class="eyebrow">Batch information</p><dl class="mt-5 divide-y divide-stone-100 text-sm"><div class="flex justify-between gap-5 py-3"><dt class="text-stone-500">SKU</dt><dd class="font-bold text-right">{{ $product->sku ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3"><dt class="text-stone-500">Batch</dt><dd class="font-bold text-right">{{ $product->batch_number ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3"><dt class="text-stone-500">Harvested</dt><dd class="font-bold text-right">{{ $product->harvested_at?->format('d M Y') ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3"><dt class="text-stone-500">Producer location</dt><dd class="font-bold text-right">{{ $product->producer_location ?: $product->origin_region ?: 'Not specified' }}</dd></div><div class="flex justify-between gap-5 py-3"><dt class="text-stone-500">Pack weight</dt><dd class="font-bold text-right">{{ $product->weight ? ((float) $product->weight >= 1000 ? rtrim(rtrim(number_format((float) $product->weight / 1000, 2), '0'), '.').' kg' : number_format((float) $product->weight, 0).' g') : 'Not specified' }}</dd></div></dl></aside>
    </div>
</section>

<section id="customer-reviews" class="section-space pb-0 scroll-mt-32">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"><div><p class="eyebrow">Verified customer feedback</p><h2 class="section-title mt-3">Customer reviews</h2></div>@if($reviewCount)<p class="text-sm text-stone-500"><strong class="text-2xl text-stone-900">{{ number_format($averageRating, 1) }}</strong> out of 5 · {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</p>@endif</div>
    <div class="mt-8 grid gap-6 lg:grid-cols-[.65fr_1.35fr]">
        <aside class="surface-card h-fit p-6"><h3 class="font-display text-2xl font-semibold">Share your experience</h3>@auth @if($canReview)<p class="mt-2 text-sm leading-6 text-stone-500">Your review will appear after moderation.</p><form method="POST" action="{{ route('reviews.store', $product) }}" class="mt-5 space-y-4">@csrf<div><label for="reviewRating" class="field-label">Rating</label><select id="reviewRating" name="rating" class="field-control" required>@foreach([5=>'Excellent — 5 stars',4=>'Very good — 4 stars',3=>'Good — 3 stars',2=>'Fair — 2 stars',1=>'Poor — 1 star'] as $value=>$label)<option value="{{ $value }}" @selected((int)old('rating',5)===$value)>{{ $label }}</option>@endforeach</select></div><div><label for="reviewTitle" class="field-label">Review title <span class="font-normal text-stone-500">(optional)</span></label><input id="reviewTitle" name="title" value="{{ old('title') }}" maxlength="150" class="field-control"></div><div><label for="reviewBody" class="field-label">Your review</label><textarea id="reviewBody" name="body" rows="5" minlength="10" maxlength="2000" class="field-control" required>{{ old('body') }}</textarea><p class="field-help">At least 10 characters. Please focus on the product.</p></div><button class="button-primary w-full" type="submit">Submit for approval</button></form>@else<p class="mt-3 text-sm leading-6 text-stone-600">Reviews are available after a verified purchase of this product.</p><a href="{{ route('orders') }}" class="button-secondary mt-5">View your orders</a>@endif @else<p class="mt-3 text-sm leading-6 text-stone-600">Sign in after receiving your order to leave a verified review.</p><a href="{{ route('login') }}" class="button-primary mt-5">Sign in</a>@endauth</aside>
        <div class="space-y-4">@forelse($product->reviews as $review)<article class="surface-card p-6"><div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-saffron" aria-label="{{ $review->rating }} out of 5 stars">@for($star=1;$star<=5;$star++)<span aria-hidden="true" class="{{ $star <= $review->rating ? '' : 'text-stone-300' }}">★</span>@endfor</p><h3 class="mt-2 text-lg font-extrabold">{{ $review->title ?: 'Customer review' }}</h3></div>@if($review->verified_purchase)<span class="rounded-full bg-brand-50 px-3 py-1 text-[11px] font-black text-brand-700">Verified purchase</span>@endif</div><p class="mt-4 leading-7 text-stone-600">{{ $review->body }}</p><p class="mt-4 text-xs text-stone-500"><strong class="text-stone-800">{{ $review->user->name }}</strong> · {{ $review->created_at->format('d M Y') }}</p></article>@empty<div class="surface-card p-10 text-center"><p class="text-3xl text-saffron" aria-hidden="true">☆</p><h3 class="font-display mt-3 text-2xl font-semibold">No approved reviews yet</h3><p class="mt-2 text-sm text-stone-500">Verified buyers can be the first to share their experience.</p></div>@endforelse</div>
    </div>
</section>

@if($relatedProducts->isNotEmpty())<section class="section-space pb-0"><div class="flex items-end justify-between gap-4"><div><p class="eyebrow">Continue exploring</p><h2 class="section-title mt-3">You may also like</h2></div><a href="{{ route('products.index') }}" class="button-quiet hidden sm:inline-flex">View all →</a></div><div class="mt-8 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">@foreach($relatedProducts as $related)<x-product-card :product="$related" />@endforeach</div></section>@endif
@endsection

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
document.querySelectorAll('[data-product-thumbnail]').forEach(button => button.addEventListener('click', () => {
    const image = document.getElementById('mainProductImage');
    if (!image) return;
    image.src = button.dataset.src;
    document.querySelectorAll('[data-product-thumbnail]').forEach(item => item.classList.remove('border-brand-600'));
    button.classList.add('border-brand-600');
}));

</script>
@endpush
