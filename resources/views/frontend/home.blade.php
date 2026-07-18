@extends('layouts.app')

@section('title', 'Pahadi Roots · Traceable produce from Uttarakhand')
@section('meta_description', 'Shop traceable mountain honey, herbal teas, pulses, grains, spices and handmade gifts sourced across Uttarakhand and Himachal Pradesh.')
@section('og_image', $heroSlides->first()->image_url)
@section('og_image_alt', $heroSlides->first()->image_alt)

@push('head')
<link rel="preload" as="image" href="{{ $heroSlides->first()->image_url }}" fetchpriority="high">
<script type="application/ld+json">{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [array_filter([
        '@type' => 'OnlineStore',
        '@id' => route('home').'#organization',
        'name' => config('seo.organization.name'),
        'url' => route('home'),
        'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/img/logo.png'), 'contentUrl' => asset('assets/img/logo.png'), 'width' => 1642, 'height' => 395],
        'image' => $heroSlides->first()->image_url,
        'description' => config('seo.default_description'),
        'email' => config('seo.organization.email'),
        'telephone' => config('seo.organization.telephone'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => config('seo.organization.street_address'), 'addressLocality' => config('seo.organization.locality'), 'addressRegion' => config('seo.organization.region'), 'postalCode' => config('seo.organization.postal_code'), 'addressCountry' => config('seo.organization.country')],
        'areaServed' => [['@type' => 'AdministrativeArea', 'name' => 'Uttarakhand'], ['@type' => 'Country', 'name' => 'India']],
        'contactPoint' => array_filter(['@type' => 'ContactPoint', 'contactType' => 'customer support', 'email' => config('seo.organization.email'), 'telephone' => config('seo.organization.telephone'), 'availableLanguage' => ['English', 'Hindi']]),
        'hasMerchantReturnPolicy' => ['@type' => 'MerchantReturnPolicy', 'applicableCountry' => 'IN', 'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow', 'merchantReturnDays' => 30, 'returnMethod' => 'https://schema.org/ReturnByMail', 'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility', 'merchantReturnLink' => route('returns.policy')],
    ]), [
        '@type' => 'WebSite',
        '@id' => route('home').'#website',
        'url' => route('home'),
        'name' => config('seo.site_name'),
        'publisher' => ['@id' => route('home').'#organization'],
        'inLanguage' => config('seo.language'),
    ]],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<section class="home-hero relative overflow-hidden rounded-2xl bg-brand-950 shadow-sm" data-hero-slider aria-roledescription="carousel" aria-label="Featured Pahadi Roots campaigns">
    <div class="relative min-h-[560px] sm:min-h-[600px] lg:min-h-[640px]" aria-live="off">
        @foreach($heroSlides as $slide)
            @php
                $isFirst = $loop->first;
                $isDarkText = $slide->text_theme === 'dark';
                $positionClasses = match($slide->text_position) {
                    'center' => 'mx-auto items-center text-center',
                    'right' => 'ml-auto items-end text-right',
                    default => 'mr-auto items-start text-left',
                };
                $primaryUrl = str_starts_with((string) $slide->primary_url, '/') ? url($slide->primary_url) : $slide->primary_url;
                $secondaryUrl = str_starts_with((string) $slide->secondary_url, '/') ? url($slide->secondary_url) : $slide->secondary_url;
            @endphp
            <article class="absolute inset-0 transition-opacity duration-700 {{ $isFirst ? 'z-10 opacity-100' : 'pointer-events-none z-0 opacity-0' }}" data-hero-slide aria-hidden="{{ $isFirst ? 'false' : 'true' }}" @if(!$isFirst) inert @endif aria-roledescription="slide" aria-label="Slide {{ $loop->iteration }} of {{ $heroSlides->count() }}">
                <img src="{{ $slide->image_url }}" data-image-fallback alt="{{ $slide->image_alt }}" class="absolute inset-0 h-full w-full object-cover" width="1920" height="900" @if($isFirst) fetchpriority="high" @else loading="lazy" decoding="async" @endif>
                <div class="absolute inset-0 {{ $isDarkText ? 'bg-gradient-to-r from-white/95 via-white/75 to-white/10' : 'bg-gradient-to-r from-brand-950/95 via-brand-950/68 to-brand-950/10' }}"></div>
                <div class="relative flex min-h-[560px] items-center px-6 pb-24 pt-16 sm:min-h-[600px] sm:px-10 lg:min-h-[640px] lg:px-16">
                    <div class="flex w-full max-w-2xl flex-col {{ $positionClasses }} {{ $isDarkText ? 'text-brand-950' : 'text-white' }}">
                        @if($slide->eyebrow)<p class="text-[11px] font-black uppercase tracking-[.18em] {{ $isDarkText ? 'text-brand-700' : 'text-amber-300' }}">{{ $slide->eyebrow }}</p>@endif
                        @if($isFirst)<h1 class="font-display mt-5 text-4xl font-semibold leading-[1.02] sm:text-5xl lg:text-6xl">{{ $slide->title }}</h1>@else<h2 class="font-display mt-5 text-4xl font-semibold leading-[1.02] sm:text-5xl lg:text-6xl">{{ $slide->title }}</h2>@endif
                        @if($slide->description)<p class="mt-6 max-w-xl text-base leading-8 {{ $isDarkText ? 'text-stone-700' : 'text-stone-100' }} sm:text-lg">{{ $slide->description }}</p>@endif
                        @if($slide->primary_label || $slide->secondary_label)<div class="mt-8 flex flex-wrap gap-3 {{ $slide->text_position === 'center' ? 'justify-center' : ($slide->text_position === 'right' ? 'justify-end' : '') }}">@if($slide->primary_label && $primaryUrl)<a href="{{ $primaryUrl }}" class="button-primary {{ $isDarkText ? '' : '!bg-white !text-brand-950 hover:!bg-brand-50' }}">{{ $slide->primary_label }} <span aria-hidden="true">→</span></a>@endif @if($slide->secondary_label && $secondaryUrl)<a href="{{ $secondaryUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border px-5 py-3 text-sm font-black {{ $isDarkText ? 'border-brand-800/25 text-brand-950 hover:bg-white/60' : 'border-white/30 text-white hover:bg-white/10' }}">{{ $slide->secondary_label }}</a>@endif</div>@endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
    @if($heroSlides->count() > 1)
        <div class="absolute inset-x-0 bottom-0 z-20 flex items-center justify-between gap-5 border-t border-white/15 bg-brand-950/45 px-5 py-4 text-white backdrop-blur-sm sm:px-8">
            <div class="flex gap-2" role="tablist" aria-label="Choose a featured slide">@foreach($heroSlides as $slide)<button type="button" class="h-1.5 rounded-full transition-all {{ $loop->first ? 'w-9 bg-amber-400' : 'w-5 bg-white/50 hover:bg-white' }}" data-hero-dot aria-label="Show slide {{ $loop->iteration }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"></button>@endforeach</div>
            <div class="flex items-center gap-2"><button type="button" class="grid h-10 w-10 place-items-center rounded-full border border-white/25 hover:bg-white/10" data-hero-previous aria-label="Previous slide"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg></button><p class="min-w-12 text-center text-xs font-black"><span data-hero-current>01</span> / {{ str_pad((string) $heroSlides->count(), 2, '0', STR_PAD_LEFT) }}</p><button type="button" class="grid h-10 w-10 place-items-center rounded-full border border-white/25 hover:bg-white/10" data-hero-next aria-label="Next slide"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 6 6 6-6 6"/></svg></button></div>
        </div>
    @endif
</section>

<section class="mt-5 grid overflow-hidden rounded-xl border border-stone-200 bg-white sm:grid-cols-3" aria-label="Store offers and benefits">
    <a href="{{ route('products.index', ['sale' => 1]) }}" class="group flex items-center gap-4 p-5 hover:bg-amber-50"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-amber-100 text-saffron" aria-hidden="true">%</span><span><strong class="block text-sm text-stone-900">Current offers</strong><small class="mt-1 block text-xs text-stone-500">Browse reduced-price products</small></span><span class="ml-auto text-brand-700 transition group-hover:translate-x-1" aria-hidden="true">→</span></a>
    <a href="{{ route('bundles.index') }}" class="group flex items-center gap-4 border-y border-stone-200 p-5 hover:bg-brand-50 sm:border-x sm:border-y-0"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-brand-100 text-brand-700" aria-hidden="true">◇</span><span><strong class="block text-sm text-stone-900">Gift-ready bundles</strong><small class="mt-1 block text-xs text-stone-500">Thoughtful collections from the hills</small></span><span class="ml-auto text-brand-700 transition group-hover:translate-x-1" aria-hidden="true">→</span></a>
    <a href="{{ route('rewards') }}" class="group flex items-center gap-4 p-5 hover:bg-brand-50"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-brand-100 text-brand-700" aria-hidden="true">★</span><span><strong class="block text-sm text-stone-900">Rewards and referrals</strong><small class="mt-1 block text-xs text-stone-500">Learn how to earn and redeem</small></span><span class="ml-auto text-brand-700 transition group-hover:translate-x-1" aria-hidden="true">→</span></a>
</section>

<section class="section-space">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="eyebrow">Browse the range</p><h2 class="section-title mt-3">Shop by category</h2></div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" class="category-carousel-control" data-carousel-previous aria-label="Show previous categories">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg>
            </button>
            <button type="button" class="category-carousel-control" data-carousel-next aria-label="Show next categories">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>
            </button>
            <a href="{{ route('products.index') }}" class="button-quiet">See everything <span aria-hidden="true">→</span></a>
        </div>
    </div>
    <div class="category-carousel mt-7" data-carousel aria-label="Product categories">
        @forelse($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-tile group w-[72%] shrink-0 snap-start overflow-hidden border border-stone-200 bg-white sm:w-[42%] md:w-[30%] lg:w-[23%] xl:w-[19%]">
                <div class="aspect-[4/3] overflow-hidden bg-stone-100">
                    @if($category->image)
                        <img src="{{ asset('storage/'.$category->image) }}" data-image-fallback alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy" decoding="async" width="640" height="480">
                    @else
                        <div class="grid h-full place-items-center bg-brand-50 text-brand-300"><svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/></svg></div>
                    @endif
                </div>
                <div class="flex items-center justify-between gap-3 p-4"><div><h3 class="text-sm font-extrabold text-stone-900 sm:text-base">{{ $category->name }}</h3><p class="mt-1 text-[11px] font-semibold text-stone-500">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</p></div><span class="text-lg text-brand-700 transition group-hover:translate-x-0.5" aria-hidden="true">→</span></div>
            </a>
        @empty
            <div class="surface-card col-span-full p-10 text-center text-stone-500">Collections are being prepared. Please check back soon.</div>
        @endforelse
    </div>
</section>

<section class="mb-14 overflow-hidden rounded-2xl bg-[#efe7d7] sm:mb-18" aria-labelledby="rewards-promo-title">
    <div class="grid lg:grid-cols-[1.08fr_.92fr]">
        <div class="relative flex items-center px-7 py-11 sm:px-10 sm:py-14 lg:px-14 lg:py-16">
            <div class="absolute -left-20 -top-24 h-64 w-64 rounded-full border-[40px] border-brand-800/5" aria-hidden="true"></div>
            <div class="relative max-w-xl"><p class="eyebrow">Pahadi Roots rewards</p><h2 id="rewards-promo-title" class="font-display mt-4 text-4xl font-semibold leading-[1.05] text-brand-950 sm:text-5xl">Share a little goodness. Earn something back.</h2><p class="mt-5 text-sm leading-7 text-stone-600 sm:text-base">Invite a friend with your personal link. After their first qualifying order, you earn <strong class="text-brand-900">{{ config('commerce.loyalty.referrer_reward') }} points</strong> and they receive <strong class="text-brand-900">{{ config('commerce.loyalty.referred_customer_reward') }} points</strong>.</p><div class="mt-7 flex flex-wrap gap-3"><a href="{{ route('rewards') }}" class="button-primary">See how it works <span aria-hidden="true">→</span></a><a href="{{ auth()->check() ? route('profile') : route('register') }}" class="button-secondary">{{ auth()->check() ? 'Get my referral link' : 'Create free account' }}</a></div><p class="mt-5 text-xs text-stone-500">No subscription. No joining fee. Rewards are applied automatically.</p></div>
        </div>
        <div class="relative min-h-[340px] overflow-hidden bg-brand-950 sm:min-h-[420px]">
            <img src="{{ asset('assets/img/products/wild-forest-honey.webp') }}" alt="Himalayan honey to share with friends and family" class="absolute inset-0 h-full w-full object-cover opacity-70" loading="lazy" decoding="async" width="900" height="900">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-950/25 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 grid grid-cols-2 gap-3 p-6 sm:p-8">
                <div class="rounded-xl border border-white/15 bg-white/10 p-5 text-white backdrop-blur-md"><span class="text-3xl font-black text-amber-300">+{{ config('commerce.loyalty.referrer_reward') }}</span><p class="mt-1 text-xs font-black uppercase tracking-wider">For you</p></div>
                <div class="rounded-xl border border-white/15 bg-white/10 p-5 text-white backdrop-blur-md"><span class="text-3xl font-black text-amber-300">+{{ config('commerce.loyalty.referred_customer_reward') }}</span><p class="mt-1 text-xs font-black uppercase tracking-wider">For your friend</p></div>
            </div>
        </div>
    </div>
</section>

<section class="grid gap-4 pb-14 sm:pb-18 lg:grid-cols-[1.15fr_.85fr]" aria-label="Featured promotions">
    <a href="{{ route('products.index', ['sale' => 1]) }}" class="promo-banner group relative min-h-[300px] overflow-hidden rounded-xl bg-brand-950 sm:min-h-[350px]">
        <img src="{{ asset('assets/img/products/wild-forest-honey.webp') }}" alt="Wild forest honey from the Himalayan region" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-[1.035]" loading="lazy" decoding="async" width="960" height="720">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-950/70 to-brand-950/10"></div>
        <div class="relative flex h-full min-h-[300px] max-w-md flex-col justify-end p-7 text-white sm:min-h-[350px] sm:p-10">
            <span class="w-fit rounded-full bg-amber-400 px-3 py-1 text-[10px] font-black uppercase tracking-[.14em] text-brand-950">Limited-time savings</span>
            <h2 class="font-display mt-4 text-3xl font-semibold leading-tight sm:text-4xl">Mountain favourites, now at friendlier prices.</h2>
            <p class="mt-3 max-w-sm text-sm leading-6 text-brand-100">Discover selected pantry staples and seasonal produce in our current offers.</p>
            <span class="mt-6 inline-flex items-center gap-2 text-sm font-black">Shop current offers <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
        </div>
    </a>

    <a href="{{ route('bundles.index') }}" class="promo-banner group relative min-h-[300px] overflow-hidden rounded-xl bg-[#6d3828] sm:min-h-[350px]">
        <img src="{{ asset('assets/img/products/munsiyari-rajma.webp') }}" alt="Munsiyari rajma, a traditional hill-grown pulse" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-[1.035]" loading="lazy" decoding="async" width="800" height="800">
        <div class="absolute inset-0 bg-gradient-to-t from-[#32150f]/95 via-[#32150f]/48 to-transparent"></div>
        <div class="relative flex h-full min-h-[300px] flex-col justify-end p-7 text-white sm:min-h-[350px] sm:p-9">
            <p class="text-[11px] font-black uppercase tracking-[.16em] text-amber-300">Made for sharing</p>
            <h2 class="font-display mt-3 text-3xl font-semibold leading-tight">Gift the taste of the hills.</h2>
            <p class="mt-3 text-sm leading-6 text-stone-200">Curated pantry boxes for celebrations, hosts and thoughtful everyday gestures.</p>
            <span class="mt-5 inline-flex items-center gap-2 text-sm font-black">Explore gift boxes <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
        </div>
    </a>
</section>

@if($featuredProducts->isNotEmpty())
<section class="pb-14 sm:pb-18">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="eyebrow">Curated essentials</p><h2 class="section-title mt-3">Popular right now</h2></div>
        <a href="{{ route('products.index') }}" class="button-secondary self-start sm:self-auto">Shop all products</a>
    </div>
    <div class="mt-7 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">@foreach($featuredProducts as $product)<x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" />@endforeach</div>
</section>
@endif

<section class="mb-14 grid overflow-hidden border border-stone-200 bg-white lg:grid-cols-2 sm:mb-18">
    <div class="relative overflow-hidden bg-brand-950 px-7 py-10 text-white sm:px-10 sm:py-12">
        <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full border-[45px] border-white/5" aria-hidden="true"></div>
        <div class="relative max-w-lg"><p class="text-[11px] font-black uppercase tracking-[.17em] text-amber-300">Thoughtful gifting</p><h2 class="font-display mt-4 text-3xl font-semibold leading-tight sm:text-4xl">{{ $featuredBundle?->name ?? 'A little piece of the hills.' }}</h2><p class="mt-4 text-sm leading-7 text-brand-100">{{ $featuredBundle?->description ?? 'Curated mountain pantry collections, ready to share with someone special.' }}</p>@if($featuredBundle)<p class="mt-5 text-2xl font-black">₹{{ number_format((float) $featuredBundle->price, 2) }}</p>@endif<a href="{{ route('bundles.index') }}" class="button-primary mt-6 !bg-white !text-brand-950 hover:!bg-brand-50">Explore gift boxes <span aria-hidden="true">→</span></a></div>
    </div>
    <div class="px-7 py-10 sm:px-10 sm:py-12">
        <p class="eyebrow">Delivery check</p><h2 class="font-display mt-4 text-3xl font-semibold text-brand-950">See if we deliver to you.</h2><p class="mt-4 max-w-lg text-sm leading-7 text-stone-600">Enter your six-digit pincode for availability, estimated delivery time and COD eligibility.</p>
        <form method="POST" action="{{ route('pincode.check') }}" class="mt-6 max-w-md" data-pincode-form>@csrf<div class="flex gap-2"><label for="homePincode" class="sr-only">Delivery pincode</label><input id="homePincode" name="pincode" inputmode="numeric" autocomplete="postal-code" pattern="[1-9][0-9]{5}" maxlength="6" placeholder="Enter pincode" class="field-control min-w-0" required><button class="button-primary shrink-0" type="submit" data-pincode-button>Check now</button></div><div data-pincode-result role="status" aria-live="polite" class="mt-3 hidden rounded-lg border p-4 text-sm"></div></form>
        <p class="mt-3 text-xs text-stone-500">We currently dispatch orders from Dehradun, Uttarakhand.</p>
    </div>
</section>

<section class="provenance-section overflow-hidden rounded-xl border border-stone-200 bg-white">
    <div class="grid lg:grid-cols-[1.02fr_.98fr] lg:items-stretch">
        <div class="provenance-gallery grid min-h-[380px] grid-cols-[1.35fr_.65fr] grid-rows-2 gap-2 bg-stone-100 p-2 sm:min-h-[500px] sm:gap-3 sm:p-3">
            <div class="relative row-span-2 overflow-hidden rounded-lg">
                <img src="{{ asset('assets/img/products/munsiyari-rajma.webp') }}" alt="Munsiyari rajma sourced from the Himalayan region" class="h-full w-full object-cover transition duration-700 hover:scale-[1.025]" loading="lazy" decoding="async" width="900" height="1100">
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-brand-950/80 to-transparent px-5 pb-5 pt-20 text-white sm:px-7 sm:pb-7">
                    <p class="text-[10px] font-black uppercase tracking-[.16em] text-amber-300">From Munsiyari, Uttarakhand</p>
                    <p class="mt-2 font-display text-2xl font-semibold">Produce with a place behind it.</p>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg"><img src="{{ asset('assets/img/products/wild-forest-honey.webp') }}" alt="Wild forest honey" class="h-full w-full object-cover transition duration-700 hover:scale-[1.035]" loading="lazy" decoding="async" width="520" height="520"></div>
            <div class="overflow-hidden rounded-lg"><img src="{{ asset('assets/img/products/dried-apricots.webp') }}" alt="Himalayan dried fruits and nuts" class="h-full w-full object-cover transition duration-700 hover:scale-[1.035]" loading="lazy" decoding="async" width="520" height="520"></div>
        </div>

        <div class="flex items-center px-7 py-11 sm:px-10 sm:py-14 lg:px-12 lg:py-16">
            <div class="w-full">
                <p class="eyebrow">The Pahadi approach</p>
                <h2 class="font-display mt-4 text-4xl font-semibold leading-[1.06] text-brand-950 sm:text-5xl">Good food should never feel anonymous.</h2>
                <p class="mt-5 max-w-xl text-sm leading-7 text-stone-600 sm:text-base">We make the journey behind our products easier to understand, with useful details about origin, batches and preparation.</p>

                <ol class="provenance-journey mt-8">
                    <li>
                        <span class="provenance-marker"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"/><circle cx="12" cy="10" r="2" stroke-width="2"/></svg></span>
                        <div><h3>Know where it comes from</h3><p>Region and producer details are shown whenever they are available.</p></div>
                    </li>
                    <li>
                        <span class="provenance-marker"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 3 8 4-8 4-8-4 8-4Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8 4 8-4"/></svg></span>
                        <div><h3>Understand the batch</h3><p>Harvest and preparation notes make each choice more transparent.</p></div>
                    </li>
                    <li>
                        <span class="provenance-marker"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg></span>
                        <div><h3>Receive it packed with care</h3><p>Every order is checked and prepared by our Dehradun team.</p></div>
                    </li>
                </ol>

                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-stone-200 pt-7">
                    <a href="{{ route('about') }}" class="button-primary">Discover our sourcing story <span aria-hidden="true">→</span></a>
                    <a href="{{ route('products.index') }}" class="button-quiet">Explore the collection</a>
                </div>
            </div>
        </div>
    </div>
</section>

@if($journalPosts->isNotEmpty())
<section class="section-space pb-0">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="eyebrow">From the journal</p><h2 class="section-title mt-3">Stories from the hills</h2><p class="mt-3 max-w-2xl text-sm leading-7 text-stone-600">Ingredient guides, regional food traditions and practical ways to use your mountain pantry.</p></div><a href="{{ route('blog') }}" class="button-secondary self-start sm:self-auto">Explore the journal <span aria-hidden="true">→</span></a></div>
    <div class="mt-7 grid gap-5 md:grid-cols-3">
        @foreach($journalPosts as $post)
            <article class="journal-card group overflow-hidden rounded-xl border border-stone-200 bg-white"><a href="{{ route('blog.show', $post->slug) }}" class="block aspect-[16/10] w-full overflow-hidden bg-stone-100">@if($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $post->image_alt }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.025]" loading="lazy" decoding="async" width="720" height="450">@endif</a><div class="p-5"><div class="flex justify-between gap-3 text-[10px] font-black uppercase tracking-wider text-brand-700"><span>{{ $post->category }}</span><span class="text-stone-400">{{ $post->reading_time }} min</span></div><h3 class="font-display mt-3 text-2xl font-semibold leading-tight text-brand-950"><a href="{{ route('blog.show', $post->slug) }}" class="hover:text-brand-700">{{ $post->title }}</a></h3><a href="{{ route('blog.show', $post->slug) }}" class="mt-5 inline-flex text-sm font-black text-brand-700">Read story <span class="ml-2" aria-hidden="true">→</span></a></div></article>
        @endforeach
    </div>
</section>
@endif

@if($latestProducts->isNotEmpty())
<section class="section-space pb-0">
    <div class="flex items-end justify-between gap-4"><div><p class="eyebrow">New arrivals</p><h2 class="section-title mt-3">Fresh from the hills</h2></div><a href="{{ route('products.index', ['sort' => 'latest']) }}" class="button-quiet hidden sm:inline-flex">View new arrivals <span aria-hidden="true">→</span></a></div>
    <div class="mt-7 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">@foreach($latestProducts as $product)<x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" />@endforeach</div>
</section>
@endif
@endsection
