@extends('layouts.app')
@section('title', 'Himalayan Gift Boxes & Food Hampers | Pahadi Roots')
@section('meta_description', 'Shop curated Himalayan food gift boxes with honey, teas, grains, spices and preserves, packed with care in Dehradun.')
@section('canonical', route('bundles.index'))
@if($bundles->first()?->image)@section('og_image', asset('storage/'.$bundles->first()->image))@section('og_image_alt', $bundles->first()->name.' Himalayan gift box')@endif
@push('head')
<x-seo-breadcrumbs :items="[['name' => 'Home', 'url' => route('home')], ['name' => 'Gift boxes', 'url' => route('bundles.index')]]" />
<x-structured-data :data="array_filter(['@context' => 'https://schema.org', '@type' => 'CollectionPage', 'name' => 'Himalayan Gift Boxes and Food Hampers', 'url' => route('bundles.index'), 'description' => 'Curated Himalayan food gift boxes packed in Dehradun.', 'primaryImageOfPage' => $bundles->first()?->image ? ['@type' => 'ImageObject', 'url' => asset('storage/'.$bundles->first()->image)] : null, 'mainEntity' => ['@type' => 'ItemList', 'numberOfItems' => $bundles->count(), 'itemListElement' => $bundles->values()->map(fn ($bundle, $index) => ['@type' => 'ListItem', 'position' => $index + 1, 'name' => $bundle->name, 'url' => route('bundles.index')])->all()]])" />
@endpush
@section('content')
@php
    $itemCounts = $bundles->map(fn ($bundle) => $bundle->products->count())->filter();
    $itemRange = $itemCounts->isEmpty() ? null : ($itemCounts->min() === $itemCounts->max() ? (string) $itemCounts->min() : $itemCounts->min().'–'.$itemCounts->max());
    $bestSaving = (float) $bundles->max('savings');
    $freeShippingThreshold = (float) config('commerce.free_shipping_threshold');
    $everyBoxShipsFree = $bundles->isNotEmpty() && $freeShippingThreshold > 0 && $bundles->every(fn ($bundle) => (float) $bundle->price > $freeShippingThreshold);
@endphp
<nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-buransh">Home</a><span>/</span><span class="text-stone-800">Gift boxes</span></nav>
<x-page-hero compact eyebrow="Gift boxes" title="Ready-made sets of hill favourites." :description="($itemRange ? 'Each gift box brings together '.$itemRange.' of our products that go well together' : 'Each gift box brings together products that go well together').', sold as one item for less than buying them separately — ready to give, or to keep.'" :image="asset('assets/img/editorial/uttarakhand-terrace-farming.webp')" image-position="center 50%" />

{{-- How gift boxes work --}}
<section class="mt-10" aria-labelledby="gift-how-title">
    <h2 id="gift-how-title" class="font-display text-3xl font-medium sm:text-4xl">How gift boxes work</h2>
    <ol class="mt-6 grid gap-4 md:grid-cols-3">
        @foreach([
            ['title' => 'Pick a themed box', 'copy' => 'Each box is built around a theme — a hill breakfast, a tea tasting, a festive hamper. Every product inside is listed on the box below.'],
            ['title' => 'Pay one lower price', 'copy' => 'The box costs less than its products bought one by one'.($bestSaving > 0 ? ' — save up to ₹'.number_format($bestSaving, 0).'.' : '.')],
            ['title' => 'We pack it together', 'copy' => 'Our team packs the whole box by hand in Dehradun.'.($everyBoxShipsFree ? ' Every box is over ₹'.number_format($freeShippingThreshold, 0).', so delivery is free.' : '')],
        ] as $step)
            <li class="surface-card flex gap-4 p-6">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-night font-display text-lg text-sun" aria-hidden="true">{{ $loop->iteration }}</span>
                <span><strong class="block text-base font-bold text-ink">{{ $step['title'] }}</strong><span class="mt-1.5 block text-sm leading-6 text-stone-600">{{ $step['copy'] }}</span></span>
            </li>
        @endforeach
    </ol>
</section>

<div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-stagger>
@forelse($bundles as $bundle)
    @php($savingPercent = $bundle->retail_value > 0 ? (int) round($bundle->savings / $bundle->retail_value * 100) : 0)
    <article class="surface-card hover-lift group flex flex-col overflow-hidden" data-product-card data-reveal>
        <div class="relative overflow-hidden">
            @if($bundle->image)<img src="{{ asset('storage/'.$bundle->image) }}" alt="{{ $bundle->name }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.025]" loading="lazy" decoding="async" width="720" height="540">@else<div class="grid aspect-[4/3] place-items-center bg-brand-50 text-buransh"><svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.5" d="M4 10h16v11H4V10Zm-2-5h20v5H2V5Zm10 16V5m0 0c-3 0-5-1-5-3 3 0 5 1 5 3Zm0 0c3 0 5-1 5-3-3 0-5 1-5 3Z"/></svg></div>@endif
            @if($bundle->savings > 0)<span class="absolute left-4 top-4 rounded-full bg-amber-400 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-ink">Save ₹{{ number_format($bundle->savings, 0) }}</span>@endif
        </div>
        <div class="flex flex-1 flex-col p-6">
            <p class="eyebrow">{{ $bundle->products->count() }} {{ Str::plural('product', $bundle->products->count()) }} inside</p>
            <h2 class="font-display mt-2 text-2xl font-semibold">{{ $bundle->name }}</h2>
            <p class="mt-3 text-sm leading-7 text-stone-600">{{ $bundle->description }}</p>
            <div class="mt-5 flex-1 border-t border-stone-100 pt-5">
                <p class="text-[11px] font-black uppercase tracking-[.16em] text-stone-500">What's inside</p>
                <ul class="mt-3 space-y-2 text-sm">@foreach($bundle->products as $product)<li class="flex justify-between gap-3 text-stone-600"><a href="{{ route('products.show', $product->slug) }}" class="hover:text-buransh hover:underline">{{ $product->name }}</a><strong>×{{ $product->pivot->quantity }}</strong></li>@endforeach</ul>
            </div>
            <div class="mt-5 rounded-2xl bg-stone-50 p-4">
                <div class="flex items-end justify-between gap-3">
                    <p><span class="text-2xl font-black">₹{{ number_format((float) $bundle->price, 2) }}</span> <span class="text-xs font-semibold text-stone-500">for the box</span></p>
                    <span class="text-right text-xs font-bold {{ $bundle->available_stock ? 'text-buransh' : 'text-red-600' }}">{{ $bundle->available_stock ? $bundle->available_stock.' available' : 'Sold out' }}</span>
                </div>
                @if($bundle->savings > 0)<p class="mt-1.5 text-xs text-stone-600">₹{{ number_format($bundle->retail_value, 2) }} if bought separately · <strong class="text-buransh">you save ₹{{ number_format($bundle->savings, 0) }}{{ $savingPercent > 0 ? ' ('.$savingPercent.'%)' : '' }}</strong></p>@endif
            </div>
            <form method="POST" action="{{ route('cart.bundles.add', $bundle) }}" class="mt-5 flex gap-2" data-add-to-cart data-open-drawer>@csrf<label for="bundle-{{ $bundle->id }}" class="sr-only">Quantity</label><input id="bundle-{{ $bundle->id }}" type="number" name="quantity" value="1" min="1" max="{{ max(1, $bundle->available_stock) }}" class="w-20 rounded-xl px-2 text-center" required><button class="button-primary flex-1" @disabled(! $bundle->available_stock)>Add gift box</button></form>
        </div>
    </article>
@empty
    <div class="surface-card col-span-full p-12 text-center text-stone-500">Gift boxes are being prepared. Please check back soon.</div>
@endforelse
</div>

{{-- Custom and bulk gifting --}}
<section class="chapter-night mt-12 flex flex-col gap-6 rounded-[2rem] p-8 sm:p-10 lg:flex-row lg:items-center lg:justify-between" aria-labelledby="gift-custom-title">
    <div class="max-w-2xl">
        <h2 id="gift-custom-title" class="font-display text-3xl font-medium text-snow">Want a different mix, or gifts in bulk?</h2>
        <p class="mt-3 text-sm leading-7 text-snow/75">Boxes come ready-made, which is how we keep the price lower. To choose every item yourself, add individual products to your cart. For festive, wedding or corporate orders, tell us what you need.</p>
    </div>
    <div class="flex flex-wrap gap-3"><a href="{{ route('products.index') }}" class="button-accent">Shop individual products</a><a href="{{ route('contact') }}" class="button-ghost-light">Ask about bulk gifting</a></div>
</section>
@endsection
