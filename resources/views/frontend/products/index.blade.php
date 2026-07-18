@extends('layouts.app')

@php
    $pageNumber = $products->currentPage();
    $listingTitle = $selectedCategory ? $selectedCategory->name.' from the Himalayas' : 'Himalayan food, produce and craft';
    $listingDescription = $selectedCategory
        ? ($selectedCategory->description ?: 'Browse '.$selectedCategory->name.' selected from Uttarakhand and neighbouring Himalayan regions.')
        : 'Browse traceable Himalayan honey, teas, pulses, grains, flours, spices, preserves, dry fruits, gifts and handmade craft.';
    if ($selectedCategory && Str::length($listingDescription) < 90) {
        $listingDescription .= ' Compare origin, ingredients, pack sizes, prices and availability across the collection.';
    }
    $filterParameters = ['search', 'sort', 'min_price', 'max_price', 'availability', 'sale', 'featured', 'origin', 'rating', 'weight'];
    $isFiltered = request()->hasAny($filterParameters);
    $activeFilterCount = collect(['category', 'search', 'min_price', 'max_price', 'availability', 'sale', 'featured', 'origin', 'rating', 'weight'])->filter(fn ($key) => request()->filled($key))->count();
    $canonicalParameters = array_filter([
        'category' => $selectedCategory?->slug,
        'page' => $pageNumber > 1 && ! $isFiltered ? $pageNumber : null,
    ]);
@endphp
@section('title', $listingTitle.($pageNumber > 1 ? ' · Page '.$pageNumber : '').' · Pahadi Roots')
@section('meta_description', Str::limit($listingDescription.($pageNumber > 1 ? ' Page '.$pageNumber.'.' : ''), 155, ''))
@section('canonical', route('products.index', $canonicalParameters))
@if($selectedCategory?->image)@section('og_image', asset('storage/'.$selectedCategory->image))@section('og_image_alt', $selectedCategory->name.' collection at Pahadi Roots')@endif
@section('pagination_links')
    @if(! $isFiltered && $products->previousPageUrl())<link rel="prev" href="{{ route('products.index', array_filter(['category' => $selectedCategory?->slug, 'page' => $pageNumber - 1 > 1 ? $pageNumber - 1 : null])) }}">@endif
    @if(! $isFiltered && $products->nextPageUrl())<link rel="next" href="{{ route('products.index', array_filter(['category' => $selectedCategory?->slug, 'page' => $pageNumber + 1])) }}">@endif
@endsection

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [[
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_values(array_filter([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => route('products.index')],
            $selectedCategory ? ['@type' => 'ListItem', 'position' => 3, 'name' => $selectedCategory->name, 'item' => route('products.index', ['category' => $selectedCategory->slug])] : null,
        ])),
    ], [
        '@type' => 'CollectionPage',
        'name' => $listingTitle,
        'description' => $listingDescription,
        'url' => route('products.index', $canonicalParameters),
        'mainEntity' => [
            '@type' => 'ItemList',
            'numberOfItems' => $products->count(),
            'itemListElement' => $products->values()->map(fn ($product, $index) => [
                '@type' => 'ListItem',
                'position' => (($pageNumber - 1) * $products->perPage()) + $index + 1,
                'url' => route('products.show', $product->slug),
                'name' => $product->name,
            ])->all(),
        ],
    ]],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-brand-700">Home</a><span aria-hidden="true">/</span>@if($selectedCategory)<a href="{{ route('products.index') }}" class="hover:text-brand-700">Shop</a><span aria-hidden="true">/</span><span class="text-stone-800">{{ $selectedCategory->name }}</span>@else<span class="text-stone-800">Shop</span>@endif</nav>

<header class="grid gap-6 border-b border-stone-200 pb-9 lg:grid-cols-[1fr_auto] lg:items-end">
    <div><p class="eyebrow">{{ $selectedCategory ? 'Himalayan collection' : 'The mountain pantry' }}</p><h1 class="display-title mt-4 max-w-4xl text-brand-950">{{ $selectedCategory ? $selectedCategory->name : 'Produce with a place, a maker and a story.' }}</h1><p class="mt-5 max-w-2xl text-base leading-8 text-stone-600">{{ $listingDescription }}</p></div>
    <div class="border-l border-stone-200 pl-5"><strong class="font-display text-4xl font-semibold text-brand-950">{{ $products->total() }}</strong><p class="mt-1 text-xs font-bold uppercase tracking-wider text-stone-500">products available</p></div>
</header>

<nav class="mt-7 flex gap-2 overflow-x-auto pb-2" aria-label="Product categories">
    <a href="{{ route('products.index', request()->except(['category','page'])) }}" class="whitespace-nowrap rounded-lg border px-4 py-2.5 text-xs font-extrabold {{ request('category') ? 'border-stone-200 bg-white text-stone-700' : 'border-brand-800 bg-brand-800 text-white' }}">All <span class="ml-1 opacity-65">{{ $categories->sum('products_count') }}</span></a>
    @foreach($categories as $category)<a href="{{ route('products.index', array_merge(request()->except(['category','page']), ['category' => $category->slug])) }}" class="whitespace-nowrap rounded-lg border px-4 py-2.5 text-xs font-extrabold {{ request('category') === $category->slug ? 'border-brand-800 bg-brand-800 text-white' : 'border-stone-200 bg-white text-stone-700 hover:border-brand-300' }}">{{ $category->name }} <span class="ml-1 opacity-60">{{ $category->products_count }}</span></a>@endforeach
</nav>

<section class="mt-6 grid gap-3 md:grid-cols-3" aria-label="Current offers">
    <a href="{{ route('products.index', ['sale' => 1]) }}" class="group rounded-xl bg-saffron px-5 py-5 text-white"><p class="text-[10px] font-black uppercase tracking-[.16em] text-amber-100">Limited-price picks</p><h2 class="mt-2 font-display text-2xl font-semibold">Shop products on sale</h2><span class="mt-3 inline-flex text-xs font-bold">See offers <span class="ml-1 transition group-hover:translate-x-1">→</span></span></a>
    <a href="{{ route('bundles.index') }}" class="group rounded-xl bg-brand-900 px-5 py-5 text-white"><p class="text-[10px] font-black uppercase tracking-[.16em] text-brand-200">Ready to share</p><h2 class="mt-2 font-display text-2xl font-semibold">Himalayan gift boxes</h2><span class="mt-3 inline-flex text-xs font-bold text-amber-300">Explore bundles <span class="ml-1 transition group-hover:translate-x-1">→</span></span></a>
    <a href="{{ auth()->check() ? route('profile') : route('register') }}" class="group rounded-xl border border-brand-200 bg-brand-50 px-5 py-5 text-brand-950"><p class="text-[10px] font-black uppercase tracking-[.16em] text-brand-600">Member rewards</p><h2 class="mt-2 font-display text-2xl font-semibold">Earn loyalty points</h2><span class="mt-3 inline-flex text-xs font-bold text-brand-700">{{ auth()->check() ? 'View rewards' : 'Create an account' }} <span class="ml-1 transition group-hover:translate-x-1">→</span></span></a>
</section>

<details class="surface-card mt-6 lg:hidden"><summary class="flex cursor-pointer list-none items-center justify-between p-4 font-extrabold text-brand-950"><span>Filter products @if($activeFilterCount)<span class="ml-2 rounded-full bg-saffron px-2 py-0.5 text-[10px] text-white">{{ $activeFilterCount }}</span>@endif</span><span class="text-xl text-brand-600" aria-hidden="true">＋</span></summary><div class="border-t border-stone-200 p-5"><x-catalogue-filters :categories="$categories" :origins="$origins" prefix="mobile-filter" /></div></details>

<div class="mt-6 grid gap-7 lg:grid-cols-[250px_minmax(0,1fr)] xl:grid-cols-[270px_minmax(0,1fr)]">
    <aside class="hidden lg:block" aria-label="Product filters"><div class="surface-card sticky top-28 max-h-[calc(100vh-8rem)] overflow-y-auto p-5"><div class="mb-5 flex items-center justify-between"><h2 class="font-display text-2xl font-semibold">Filters</h2>@if($activeFilterCount)<span class="rounded-full bg-brand-100 px-2.5 py-1 text-[10px] font-black text-brand-700">{{ $activeFilterCount }} active</span>@endif</div><x-catalogue-filters :categories="$categories" :origins="$origins" prefix="desktop-filter" /></div></aside>

    <section id="products" class="min-w-0">
        <form method="GET" action="{{ route('products.index') }}" class="surface-card mb-6 grid gap-3 p-3 sm:grid-cols-[1fr_190px_auto]" aria-label="Search and sort products">
            @foreach(request()->except(['search','sort','page']) as $key=>$value)@if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach
            <div><label for="product-search" class="sr-only">Search products</label><input id="product-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search products or origins…" class="field-control"></div>
            <div><label for="product-sort" class="sr-only">Sort products</label><select id="product-sort" name="sort" class="field-control"><option value="latest" @selected(!request('sort') || request('sort') === 'latest')>Newest first</option><option value="price_low" @selected(request('sort') === 'price_low')>Price: low to high</option><option value="price_high" @selected(request('sort') === 'price_high')>Price: high to low</option><option value="name" @selected(request('sort') === 'name')>Name: A to Z</option></select></div>
            <button class="button-primary" type="submit">Update</button>
        </form>

        <div class="mb-5 flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">{{ request('category') ? 'Selected collection' : 'All products' }}</p>@if(request('search'))<h2 class="mt-2 text-lg font-black">Results for “{{ request('search') }}”</h2>@elseif($activeFilterCount)<h2 class="mt-2 text-lg font-black">Filtered products</h2>@endif @if($activeFilterCount)<a href="{{ route('products.index') }}" class="mt-2 inline-flex text-xs font-bold text-brand-700 underline">Clear all filters</a>@endif</div><p class="text-xs font-semibold text-stone-500">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}</p></div>
        @if($products->isNotEmpty())
            <div class="grid grid-cols-2 gap-3 sm:gap-5 xl:grid-cols-3">@foreach($products as $product)<x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" />@endforeach</div>
            @if($products->hasPages())<div class="mt-10 surface-card p-4">{{ $products->links() }}</div>@endif
        @else
            <div class="surface-card px-6 py-16 text-center"><svg class="mx-auto h-12 w-12 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.5" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg><h2 class="font-display mt-5 text-2xl font-semibold">No matching products</h2><p class="mt-2 text-sm text-stone-500">Try a wider price range or remove one of the selected filters.</p><a href="{{ route('products.index') }}" class="button-primary mt-6">Clear all filters</a></div>
        @endif
    </section>
</div>

@if($selectedCategory)
<section class="mt-12 grid gap-6 border-t border-stone-200 pt-10 lg:grid-cols-[1fr_1.3fr]">
    <div><p class="eyebrow">About this collection</p><h2 class="font-display mt-3 text-3xl font-semibold text-brand-950">Choosing {{ strtolower($selectedCategory->name) }}</h2></div>
    <div><p class="leading-8 text-stone-600">{{ $selectedCategory->description }} Product pages include available origin, ingredients, producer and pack information so you can compare the range clearly.</p><div class="mt-5 flex flex-wrap gap-3"><a href="{{ route('about') }}" class="button-quiet !px-0">How we source →</a><a href="{{ route('shipping.policy') }}" class="button-quiet">Delivery information →</a></div></div>
</section>
@endif
@endsection
