@extends('layouts.app')

@php
    $pageNumber = $products->currentPage();
    $listingTitle = $selectedCategory ? ($selectedCategory->seo_title ?: $selectedCategory->name.' from the Himalayas') : 'Himalayan Food, Produce and Craft Online';
    $listingDescription = $selectedCategory
        ? ($selectedCategory->meta_description ?: $selectedCategory->description ?: 'Browse '.$selectedCategory->name.' selected from Uttarakhand and neighbouring Himalayan regions.')
        : 'Browse traceable Himalayan honey, teas, pulses, grains, flours, spices, preserves, dry fruits, gifts and handmade craft.';
    if ($selectedCategory && Str::length($listingDescription) < 90) {
        $listingDescription .= ' Compare origin, ingredients, pack sizes, prices and availability across the collection.';
    }
    $filterParameters = ['search', 'sort', 'min_price', 'max_price', 'availability', 'sale', 'featured', 'origin', 'rating', 'weight'];
    $isFiltered = request()->hasAny($filterParameters);
    $activeFilterCount = collect(['category', 'search', 'min_price', 'max_price', 'availability', 'sale', 'featured', 'origin', 'rating', 'weight'])->filter(fn ($key) => request()->filled($key))->count();
    $filterChips = collect([
        'category' => $selectedCategory?->name,
        'search' => request('search') ? 'Search: '.request('search') : null,
        'min_price' => request('min_price') ? 'From ₹'.request('min_price') : null,
        'max_price' => request('max_price') ? 'Up to ₹'.request('max_price') : null,
        'availability' => request('availability') ? str(request('availability'))->replace('_', ' ')->title() : null,
        'sale' => request()->boolean('sale') ? 'On sale' : null,
        'featured' => request()->boolean('featured') ? 'Featured' : null,
        'origin' => request('origin') ? 'Origin: '.request('origin') : null,
        'rating' => request('rating') ? request('rating').'★ & up' : null,
        'weight' => request('weight') ? 'Pack: '.str(request('weight'))->replace(['_', 'plus'], ['–', '+'])->replace('under–250', 'under 250 g')->replace('250–499', '250–499 g')->replace('500–999', '500–999 g')->replace('1000–+', '1 kg+') : null,
    ])->filter();
    $canonicalParameters = array_filter([
        'category' => $selectedCategory?->slug,
        'page' => $pageNumber > 1 && ! $isFiltered ? $pageNumber : null,
    ]);
@endphp
@section('title', $listingTitle.($pageNumber > 1 ? ' | Page '.$pageNumber : '').($selectedCategory?->seo_title ? '' : ' | Pahadi Roots'))
@section('meta_description', Str::limit($listingDescription.($pageNumber > 1 ? ' Page '.$pageNumber.'.' : ''), 155, ''))
@section('canonical', route('products.index', $canonicalParameters))
@if($selectedCategory?->image)@section('og_image', asset('storage/'.$selectedCategory->image))@section('og_image_alt', $selectedCategory->name.' collection at Pahadi Roots')@endif
@section('pagination_links')
    @if(! $isFiltered && $products->previousPageUrl())<link rel="prev" href="{{ route('products.index', array_filter(['category' => $selectedCategory?->slug, 'page' => $pageNumber - 1 > 1 ? $pageNumber - 1 : null])) }}">@endif
    @if(! $isFiltered && $products->nextPageUrl())<link rel="next" href="{{ route('products.index', array_filter(['category' => $selectedCategory?->slug, 'page' => $pageNumber + 1])) }}">@endif
@endsection

@push('head')
<x-structured-data :data="[
    '@context' => 'https://schema.org',
    '@graph' => [[
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_values(array_filter([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => route('products.index')],
            $selectedCategory ? ['@type' => 'ListItem', 'position' => 3, 'name' => $selectedCategory->name, 'item' => route('products.index', ['category' => $selectedCategory->slug])] : null,
        ])),
    ], array_filter([
        '@type' => 'CollectionPage',
        'name' => $listingTitle,
        'description' => $listingDescription,
        'url' => route('products.index', $canonicalParameters),
        'primaryImageOfPage' => $selectedCategory?->image ? ['@type' => 'ImageObject', 'url' => asset('storage/'.$selectedCategory->image)] : null,
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
    ])],
]" />
@endpush

@section('content')
<nav class="mb-8 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="link-underline hover:text-brand-700">Home</a><span aria-hidden="true" class="text-stone-300">/</span>@if($selectedCategory)<a href="{{ route('products.index') }}" class="link-underline hover:text-brand-700">Shop</a><span aria-hidden="true" class="text-stone-300">/</span><span class="text-stone-800">{{ $selectedCategory->name }}</span>@else<span class="text-stone-800">Shop</span>@endif</nav>

<header class="relative overflow-hidden rounded-[2rem] bg-brand-950 px-7 py-12 text-white sm:px-12 sm:py-16">
    @if($selectedCategory?->image)<img src="{{ asset('storage/'.$selectedCategory->image) }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-35" width="1400" height="600" data-parallax="0.06">@else<img src="{{ asset('assets/img/editorial/uttarakhand-terrace-farming.webp') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30" width="1400" height="600" data-parallax="0.06">@endif
    <div class="absolute inset-0 bg-gradient-to-r from-brand-950 via-brand-950/80 to-brand-950/20" aria-hidden="true"></div>
    <div class="relative grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
        <div><p class="eyebrow eyebrow-line !text-marigold-300">{{ $selectedCategory ? 'Himalayan collection' : 'The mountain pantry' }}</p><h1 class="display-title mt-5 max-w-4xl text-white">{{ $selectedCategory ? $selectedCategory->name : 'Produce with a place, a maker and a story.' }}</h1><p class="mt-5 max-w-2xl text-base leading-8 text-white/75">{{ $listingDescription }}</p></div>
        <div class="surface-glass rounded-[1.5rem] px-6 py-5"><strong class="stat-number block text-white"><span data-count-to="{{ $products->total() }}">{{ $products->total() }}</span></strong><p class="mt-2 text-[11px] font-black uppercase tracking-[.16em] text-white/70">products available</p></div>
    </div>
</header>

<nav class="mt-8 flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none]" aria-label="Product categories">
    <a href="{{ route('products.index') }}" class="chip shrink-0" @unless(request('category')) aria-current="page" @endunless>All <small>{{ $categories->sum('products_count') }}</small></a>
    @foreach($categories as $category)<a href="{{ route('products.index', ['category' => $category->slug]) }}" class="chip shrink-0" @if(request('category') === $category->slug) aria-current="page" @endif>{{ $category->name }} <small>{{ $category->products_count }}</small></a>@endforeach
</nav>

<section class="catalogue-promos mt-6 flex snap-x gap-3 overflow-x-auto pb-2 md:grid md:grid-cols-3 md:overflow-visible md:pb-0" aria-label="Current offers">
    <a href="{{ route('products.index', ['sale' => 1]) }}" rel="nofollow" class="hover-lift group relative overflow-hidden rounded-[1.5rem] bg-gradient-to-br from-marigold-300 to-saffron px-6 py-6 text-brand-950"><p class="text-[10px] font-black uppercase tracking-[.18em] text-clay-900/70">Limited-price picks</p><h2 class="mt-2 font-display text-2xl font-medium">Shop products on sale</h2><span class="mt-3 inline-flex items-center gap-1 text-xs font-bold">See offers <span class="button-arrow" aria-hidden="true">→</span></span></a>
    <a href="{{ route('bundles.index') }}" class="hover-lift group relative overflow-hidden rounded-[1.5rem] bg-brand-900 px-6 py-6 text-white"><p class="text-[10px] font-black uppercase tracking-[.18em] text-brand-200">Ready to share</p><h2 class="mt-2 font-display text-2xl font-medium">Himalayan gift boxes</h2><span class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-marigold-300">Explore bundles <span class="button-arrow" aria-hidden="true">→</span></span></a>
    <a href="{{ auth()->check() ? route('profile') : route('register') }}" class="hover-lift group relative overflow-hidden rounded-[1.5rem] border border-brand-200 bg-brand-50 px-6 py-6 text-brand-950"><p class="text-[10px] font-black uppercase tracking-[.18em] text-brand-600">Member rewards</p><h2 class="mt-2 font-display text-2xl font-medium">Earn loyalty points</h2><span class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-brand-700">{{ auth()->check() ? 'View rewards' : 'Create an account' }} <span class="button-arrow" aria-hidden="true">→</span></span></a>
</section>

@if($filterChips->isNotEmpty())
    <div class="mt-5 flex flex-wrap items-center gap-2" aria-label="Active filters">
        <span class="mr-1 text-[10px] font-black uppercase tracking-widest text-stone-500">Active</span>
        @foreach($filterChips as $key => $label)<a href="{{ route('products.index', request()->except([$key, 'page'])) }}" rel="nofollow" class="active-filter-chip" aria-label="Remove {{ $label }} filter"><span>{{ $label }}</span><span aria-hidden="true">×</span></a>@endforeach
        <a href="{{ route('products.index') }}" class="ml-1 text-xs font-black text-brand-700 underline underline-offset-4">Clear all</a>
    </div>
@endif

<button type="button" class="button-secondary mt-6 w-full lg:hidden" data-drawer-open="mobileFilters" aria-controls="mobileFilters" aria-expanded="false"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M7 12h10M10 18h4"/></svg>Filter products @if($activeFilterCount)<span class="rounded-full bg-saffron px-2 py-0.5 text-[10px] text-white">{{ $activeFilterCount }}</span>@endif</button>
<aside id="mobileFilters" class="drawer drawer-left lg:hidden" aria-label="Filter products" data-drawer>
    <div class="flex items-center justify-between border-b border-stone-200/80 px-6 py-4"><p class="font-display text-2xl font-medium text-brand-950">Filters</p><button type="button" class="icon-button" aria-label="Close filters" data-drawer-close><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18"/></svg></button></div>
    <div class="flex-1 overflow-y-auto p-5"><x-catalogue-filters :categories="$categories" :origins="$origins" prefix="mobile-filter" /></div>
</aside>

<div class="mt-6 grid gap-7 lg:grid-cols-[300px_minmax(0,1fr)] xl:grid-cols-[320px_minmax(0,1fr)]">
    <aside class="hidden lg:block" aria-label="Product filters"><div class="catalogue-filter-panel surface-card sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto p-5 xl:p-6"><div class="mb-5 flex items-start justify-between gap-3 border-b border-stone-200 pb-4"><div><p class="eyebrow !text-[10px]">Refine your shop</p><h2 class="font-display mt-1 text-2xl font-semibold">Filters</h2></div>@if($activeFilterCount)<span class="rounded-full bg-brand-100 px-2.5 py-1 text-[10px] font-black text-brand-700">{{ $activeFilterCount }} active</span>@endif</div><x-catalogue-filters :categories="$categories" :origins="$origins" prefix="desktop-filter" /></div></aside>

    <section id="products" class="min-w-0">
        <form method="GET" action="{{ route('products.index') }}" class="surface-card mb-6 grid gap-3 p-3 sm:grid-cols-[1fr_190px_auto]" aria-label="Search and sort products">
            @foreach(request()->except(['search','sort','page']) as $key=>$value)@if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach
            <div><label for="product-search" class="sr-only">Search products</label><input id="product-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search products or origins…" class="field-control"></div>
            <div><label for="product-sort" class="sr-only">Sort products</label><select id="product-sort" name="sort" class="field-control catalogue-select" data-auto-submit><option value="latest" @selected(!request('sort') || request('sort') === 'latest')>Newest first</option><option value="price_low" @selected(request('sort') === 'price_low')>Price: low to high</option><option value="price_high" @selected(request('sort') === 'price_high')>Price: high to low</option><option value="name" @selected(request('sort') === 'name')>Name: A to Z</option></select></div>
            <button class="button-primary" type="submit">Update</button>
        </form>

        <div class="mb-5 flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">{{ request('category') ? 'Selected collection' : 'All products' }}</p>@if(request('search'))<h2 class="mt-2 text-lg font-black">Results for “{{ request('search') }}”</h2>@elseif($activeFilterCount)<h2 class="mt-2 text-lg font-black">Filtered products</h2>@endif @if($activeFilterCount)<a href="{{ route('products.index') }}" class="mt-2 inline-flex text-xs font-bold text-brand-700 underline">Clear all filters</a>@endif</div><p class="text-xs font-semibold text-stone-500">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}</p></div>
        @if($products->isNotEmpty())
            <div class="grid grid-cols-2 gap-3 sm:gap-5 xl:grid-cols-3" data-reveal-stagger>@foreach($products as $product)<div data-reveal style="--i: {{ $loop->index % 3 }}"><x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" /></div>@endforeach</div>
            @if($products->hasPages())<div class="mt-10 surface-card p-4">{{ $products->links() }}</div>@endif
        @else
            <div class="surface-card px-6 py-16 text-center"><span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-brand-50 text-brand-400"><svg class="h-9 w-9 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.5" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg></span><h2 class="font-display mt-5 text-3xl font-medium">No matching products</h2><p class="mt-2 text-sm text-stone-500">Try a wider price range or remove one of the selected filters.</p><a href="{{ route('products.index') }}" class="button-primary mt-6">Clear all filters</a></div>
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
