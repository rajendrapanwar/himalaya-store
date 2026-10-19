@extends('layouts.app')

@section('title', 'Himalayan Food from Uttarakhand | Pahadi Roots')
@section('meta_description', 'Shop traceable mountain honey, herbal teas, pulses, grains, spices and handmade gifts sourced across Uttarakhand and Himachal Pradesh.')
@section('og_image', $heroSlides->first()->image_url)
@section('og_image_alt', $heroSlides->first()->image_alt)
@section('full_bleed', '1')

@php
    $productTabs = collect([
        ['id' => 'popular', 'label' => 'Popular', 'products' => $featuredProducts],
        ['id' => 'new', 'label' => 'New arrivals', 'products' => $latestProducts->take(8)],
        ['id' => 'sale', 'label' => 'On sale', 'products' => $saleProducts],
    ])->filter(fn ($tab) => $tab['products']->isNotEmpty())->values();
    $fallbackCategoryImages = ['editorial/uttarakhand-terrace-farming.webp', 'editorial/misty-tea-mountains.webp', 'editorial/tea-farmer-hillside.webp', 'editorial/rolling-cultivated-hills.webp', 'editorial/darjeeling-tea-garden.webp'];
    $freeDeliveryThreshold = '₹'.number_format((float) config('commerce.free_shipping_threshold'), 0);
@endphp

@push('head')
<link rel="preload" as="image" href="{{ $heroSlides->first()->image_url }}" fetchpriority="high">
<x-structured-data :data="[
    '@context' => 'https://schema.org',
    '@graph' => [array_filter([
        '@type' => 'OnlineStore',
        '@id' => route('home').'#organization',
        'name' => config('seo.organization.name'),
        'legalName' => config('seo.organization.legal_name'),
        'foundingDate' => config('seo.organization.founding_date'),
        'url' => route('home'),
        'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/img/logo.png'), 'contentUrl' => asset('assets/img/logo.png'), 'width' => 1642, 'height' => 395],
        'image' => $heroSlides->first()->image_url,
        'description' => config('seo.default_description'),
        'email' => config('seo.organization.email'),
        'telephone' => config('seo.organization.telephone'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => config('seo.organization.street_address'), 'addressLocality' => config('seo.organization.locality'), 'addressRegion' => config('seo.organization.region'), 'postalCode' => config('seo.organization.postal_code'), 'addressCountry' => config('seo.organization.country')],
        'areaServed' => [['@type' => 'AdministrativeArea', 'name' => 'Uttarakhand'], ['@type' => 'Country', 'name' => 'India']],
        'contactPoint' => array_filter(['@type' => 'ContactPoint', 'contactType' => 'customer support', 'email' => config('seo.organization.email'), 'telephone' => config('seo.organization.telephone'), 'availableLanguage' => ['English', 'Hindi']]),
        'sameAs' => collect(['instagram', 'facebook', 'youtube', 'pinterest'])->map(fn ($network) => config('services.social.'.$network))->filter()->values()->all(),
        'hasMerchantReturnPolicy' => ['@type' => 'MerchantReturnPolicy', 'applicableCountry' => 'IN', 'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow', 'merchantReturnDays' => 30, 'returnMethod' => 'https://schema.org/ReturnByMail', 'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility', 'merchantReturnLink' => route('returns.policy')],
        'hasMemberProgram' => ['@type' => 'MemberProgram', '@id' => route('rewards').'#program', 'name' => 'Pahadi Roots Rewards', 'description' => 'A free loyalty programme where registered shoppers earn points on eligible purchases and can redeem their balance at checkout.', 'url' => route('rewards'), 'hasTiers' => [['@type' => 'MemberProgramTier', '@id' => route('rewards').'#member-tier', 'name' => 'Member', 'hasTierBenefit' => 'https://schema.org/TierBenefitLoyaltyPoints', 'membershipPointsEarned' => (float) config('commerce.loyalty.points_per_rupee', 0.1)]],
        ],
    ]), [
        '@type' => 'WebSite',
        '@id' => route('home').'#website',
        'url' => route('home'),
        'name' => config('seo.site_name'),
        'publisher' => ['@id' => route('home').'#organization'],
        'inLanguage' => config('seo.language'),
    ]],
]" />
@endpush

@section('content')
{{-- ───────────── Hero ───────────── --}}
<section class="hero" data-hero-slider data-hero-delay="7000" aria-roledescription="carousel" aria-label="Featured Pahadi Roots campaigns">
    <div class="relative h-[calc(100svh-7rem)] min-h-[620px] max-h-[880px]">
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
            <article class="hero-slide {{ $isFirst ? 'is-active' : '' }}" data-hero-slide aria-hidden="{{ $isFirst ? 'false' : 'true' }}" @if(!$isFirst) inert @endif aria-roledescription="slide" aria-label="Slide {{ $loop->iteration }} of {{ $heroSlides->count() }}">
                <img src="{{ $slide->image_url }}" data-image-fallback alt="{{ $slide->image_alt }}" class="hero-media object-cover" width="1920" height="900" @if($isFirst) fetchpriority="high" @else loading="lazy" decoding="async" @endif data-parallax="0.12">
                <div class="absolute inset-0 {{ $isDarkText ? 'bg-gradient-to-r from-white/95 via-white/70 to-white/0' : 'bg-gradient-to-r from-brand-950/90 via-brand-950/55 to-brand-950/0' }}"></div>
                <div class="absolute inset-0 bg-gradient-to-t {{ $isDarkText ? 'from-white/50' : 'from-brand-950/80' }} via-transparent to-transparent"></div>
                <div class="relative z-10 flex h-full items-center pb-32 pt-16">
                    <div class="site-shell">
                        <div class="hero-copy flex w-full max-w-3xl flex-col {{ $positionClasses }} {{ $isDarkText ? 'text-brand-950' : 'text-white' }}">
                            @if($slide->eyebrow)<p class="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[.24em] {{ $isDarkText ? 'text-brand-700' : 'text-marigold-300' }}"><span class="h-px w-8 bg-current opacity-70" aria-hidden="true"></span>{{ $slide->eyebrow }}</p>@endif
                            @if($isFirst)<h1 class="font-display mt-6 text-[clamp(2.9rem,7vw,6.25rem)] font-medium leading-[.95] tracking-[-.035em]" data-split>{{ $slide->title }}</h1>@else<h2 class="font-display mt-6 text-[clamp(2.9rem,7vw,6.25rem)] font-medium leading-[.95] tracking-[-.035em]" data-split>{{ $slide->title }}</h2>@endif
                            @if($slide->description)<p class="mt-7 max-w-xl text-base leading-8 {{ $isDarkText ? 'text-stone-700' : 'text-white/85' }} sm:text-lg">{{ $slide->description }}</p>@endif
                            @if($slide->primary_label || $slide->secondary_label)<div class="mt-9 flex flex-wrap gap-3 {{ $slide->text_position === 'center' ? 'justify-center' : ($slide->text_position === 'right' ? 'justify-end' : '') }}">@if($slide->primary_label && $primaryUrl)<a href="{{ $primaryUrl }}" class="{{ $isDarkText ? 'button-primary' : 'button-accent' }}" data-magnetic>{{ $slide->primary_label }} <span class="button-arrow" aria-hidden="true">→</span></a>@endif @if($slide->secondary_label && $secondaryUrl)<a href="{{ $secondaryUrl }}" class="{{ $isDarkText ? 'button-secondary' : 'button-ghost-light' }}">{{ $slide->secondary_label }}</a>@endif</div>@endif
                        </div>
                    </div>
                </div>
            </article>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 z-20 pb-7 sm:pb-9">
            <div class="site-shell flex items-end justify-between gap-6 pr-16 sm:pr-0">
                @if($heroSlides->count() > 1)
                    <div class="flex w-full max-w-lg items-center gap-4 text-white">
                        <button type="button" class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-white/30 backdrop-blur transition hover:bg-white/15" data-hero-toggle aria-label="Pause slideshow" aria-pressed="false"><svg class="h-4 w-4" data-icon-pause fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg><svg class="hidden h-4 w-4" data-icon-play fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button>
                        <div class="grid flex-1 gap-2" style="grid-template-columns: repeat({{ $heroSlides->count() }}, minmax(0, 1fr))" role="tablist" aria-label="Choose a featured slide">
                            @foreach($heroSlides as $slide)
                                <button type="button" class="group py-3 text-left" data-hero-dot role="tab" aria-label="Show slide {{ $loop->iteration }}: {{ $slide->title }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"><span class="hero-progress block {{ $loop->first ? 'is-active' : '' }}" data-hero-progress></span><span class="mt-2 hidden truncate text-[11px] font-bold text-white/60 transition group-hover:text-white sm:block">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} · {{ $slide->eyebrow ?: Str::limit($slide->title, 24) }}</span></button>
                            @endforeach
                        </div>
                        <div class="hidden shrink-0 gap-2 sm:flex"><button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-white/30 backdrop-blur transition hover:bg-white hover:text-brand-950" data-hero-previous aria-label="Previous slide"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg></button><button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-white/30 backdrop-blur transition hover:bg-white hover:text-brand-950" data-hero-next aria-label="Next slide"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 6 6 6-6 6"/></svg></button></div>
                    </div>
                @else
                    <a href="#home-intro" class="scroll-cue hidden text-white/80 hover:text-white md:inline-flex"><span>Scroll</span><span class="line" aria-hidden="true"></span></a>
                @endif
                <div class="surface-glass hidden w-[300px] shrink-0 rounded-[1.5rem] p-5 text-white xl:block" data-reveal="fade">
                    <p class="text-[10px] font-black uppercase tracking-[.2em] text-marigold-300">The mountain pantry</p>
                    <dl class="mt-3 grid grid-cols-2 gap-4">
                        <div><dt class="sr-only">Products</dt><dd class="font-display text-3xl">{{ $stats['products'] }}</dd><p class="text-[11px] text-white/70">small-batch products</p></div>
                        <div><dt class="sr-only">Collections</dt><dd class="font-display text-3xl">{{ $stats['collections'] }}</dd><p class="text-[11px] text-white/70">collections</p></div>
                    </dl>
                    <p class="mt-4 border-t border-white/15 pt-3 text-xs text-white/80">Free delivery over {{ $freeDeliveryThreshold }} · Packed in Dehradun</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ───────────── Values marquee ───────────── --}}
<section class="mt-6 overflow-hidden py-5" aria-label="Why shop with Pahadi Roots">
    <div class="marquee text-brand-900" data-marquee>
        @foreach([false, true] as $duplicate)
            <ul class="marquee-track" @if($duplicate) aria-hidden="true" @endif>
                @foreach(['Traceable origin', 'Small-batch harvests', 'Packed by hand in Dehradun', 'Free delivery over '.$freeDeliveryThreshold, '30-day returns', 'Cards, UPI & cash on delivery', 'Loyalty points on every order'] as $value)
                    <li class="marquee-item font-display text-2xl italic sm:text-3xl"><span>{{ $value }}</span><span class="text-saffron not-italic" aria-hidden="true">✦</span></li>
                @endforeach
            </ul>
        @endforeach
    </div>
</section>

{{-- ───────────── Intro + stats ───────────── --}}
<section id="home-intro" class="site-shell section-space !pt-12">
    <div class="grid gap-12 lg:grid-cols-[1.25fr_1fr] lg:items-end">
        <div data-reveal>
            <p class="eyebrow eyebrow-line">Rooted in the hills</p>
            <h2 class="section-title mt-5 max-w-3xl">Good food should never feel <em>anonymous.</em> We trace every jar, sack and basket back to the slopes it came from.</h2>
        </div>
        <div class="grid grid-cols-2 gap-4" data-reveal-stagger>
            @foreach([
                ['value' => $stats['products'], 'suffix' => '', 'label' => 'Products from mountain producers'],
                ['value' => $stats['collections'], 'suffix' => '', 'label' => 'Curated pantry collections'],
                ['value' => max($stats['regions'], 1), 'suffix' => '+', 'label' => 'Himalayan regions represented'],
                ['value' => 30, 'suffix' => '', 'label' => 'Day returns on eligible orders'],
            ] as $stat)
                <div class="rounded-[1.5rem] border border-stone-200/80 bg-white p-6 shadow-[var(--shadow-xs)] transition duration-500 hover:-translate-y-1 hover:shadow-[var(--shadow-md)]" data-reveal>
                    <p class="stat-number text-brand-950"><span data-count-to="{{ $stat['value'] }}">{{ $stat['value'] }}</span>{{ $stat['suffix'] }}</p>
                    <p class="mt-3 text-xs font-semibold leading-5 text-stone-500">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ───────────── Collections carousel ───────────── --}}
@if($categories->isNotEmpty())
<section class="site-shell pb-[clamp(3.5rem,7vw,6.5rem)]" aria-labelledby="collections-title">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between" data-reveal>
        <div><p class="eyebrow eyebrow-line">Browse the range</p><h2 id="collections-title" class="section-title mt-4">Shop by <em>collection</em></h2></div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" class="category-carousel-control" data-carousel-toggle aria-label="Pause automatic scrolling" aria-pressed="false"><svg class="h-4 w-4" data-icon-pause fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg><svg class="hidden h-4 w-4" data-icon-play fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button>
            <button type="button" class="category-carousel-control" data-carousel-previous aria-label="Show previous collections"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg></button>
            <button type="button" class="category-carousel-control" data-carousel-next aria-label="Show next collections"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg></button>
        </div>
    </div>
    <div class="category-carousel mt-9" data-carousel data-carousel-autoplay="4800" aria-label="Product collections" aria-roledescription="carousel">
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-tile group aspect-[3/4] w-[70%] shrink-0 snap-start sm:w-[42%] md:w-[30%] lg:w-[23%]">
                <img src="{{ $category->image ? asset('storage/'.$category->image) : asset('assets/img/'.$fallbackCategoryImages[$loop->index % count($fallbackCategoryImages)]) }}" data-image-fallback alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async" width="600" height="800">
                <span class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/20 to-transparent" aria-hidden="true"></span>
                <span class="absolute left-4 top-4 rounded-full bg-white/15 px-3 py-1 text-[10px] font-black uppercase tracking-[.16em] text-white backdrop-blur-md">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span>
                <span class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-5">
                    <span class="font-display text-2xl font-medium leading-tight sm:text-[1.7rem]">{{ $category->name }}</span>
                    <span class="tile-arrow grid h-11 w-11 shrink-0 place-items-center rounded-full bg-white/15 text-white backdrop-blur-md" aria-hidden="true"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
                </span>
            </a>
        @endforeach
    </div>
    <div class="carousel-track mt-2 max-w-xs" data-carousel-progress aria-hidden="true"><span></span></div>
</section>
@endif

{{-- ───────────── Tabbed products ───────────── --}}
@if($productTabs->isNotEmpty())
<section class="bg-parchment py-[clamp(3.5rem,7vw,6.5rem)]" aria-labelledby="favourites-title">
    <div class="site-shell">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between" data-reveal>
            <div><p class="eyebrow eyebrow-line">Curated essentials</p><h2 id="favourites-title" class="section-title mt-4">This season’s <em>favourites</em></h2></div>
            @if($productTabs->count() > 1)
                <div class="tabs self-start md:self-auto" role="tablist" aria-label="Product groups" data-tabs>
                    <span class="tab-indicator" aria-hidden="true"></span>
                    @foreach($productTabs as $tab)<button type="button" class="tab" role="tab" id="tab-{{ $tab['id'] }}" aria-controls="panel-{{ $tab['id'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $tab['label'] }}</button>@endforeach
                </div>
            @endif
        </div>
        @foreach($productTabs as $tab)
            <div id="panel-{{ $tab['id'] }}" role="tabpanel" aria-labelledby="tab-{{ $tab['id'] }}" class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4" @unless($loop->first) hidden @endunless @if($loop->first) data-reveal-stagger @endif>
                @foreach($tab['products'] as $product)<div style="--i: {{ $loop->index }}" @if($loop->parent->first) data-reveal @endif><x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" /></div>@endforeach
            </div>
        @endforeach
        <div class="mt-12 text-center" data-reveal><a href="{{ route('products.index') }}" class="button-primary" data-magnetic>Shop all {{ $stats['products'] }} products <span class="button-arrow" aria-hidden="true">→</span></a></div>
    </div>
</section>
@endif

{{-- ───────────── Promotions ───────────── --}}
<section class="site-shell section-space grid gap-5 lg:grid-cols-[1.2fr_.8fr]" aria-label="Featured promotions">
    <a href="{{ route('products.index', ['sale' => 1]) }}" rel="nofollow" class="promo-banner group relative min-h-[420px] overflow-hidden rounded-[2rem] bg-brand-950 sm:min-h-[500px]" data-reveal="left">
        <img src="{{ asset('assets/img/products/wild-forest-honey.webp') }}" alt="Wild forest honey from the Himalayan region" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async" width="960" height="720">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-950/60 to-transparent"></div>
        <div class="relative flex h-full min-h-[420px] max-w-md flex-col justify-end p-8 text-white sm:min-h-[500px] sm:p-12">
            <span class="w-fit rounded-full bg-marigold-300 px-3.5 py-1.5 text-[10px] font-black uppercase tracking-[.16em] text-brand-950">Limited-time savings</span>
            <h2 class="font-display mt-5 text-4xl font-medium leading-[1.02] sm:text-5xl">Mountain favourites, at <em class="text-marigold-300">friendlier</em> prices.</h2>
            <p class="mt-4 max-w-sm text-sm leading-7 text-white/80">Selected pantry staples and seasonal produce, while this harvest lasts.</p>
            <span class="mt-7 inline-flex items-center gap-2 text-sm font-black">Shop current offers <span class="button-arrow" aria-hidden="true">→</span></span>
        </div>
    </a>
    <a href="{{ route('bundles.index') }}" class="promo-banner group relative min-h-[420px] overflow-hidden rounded-[2rem] bg-clay-900 sm:min-h-[500px]" data-reveal="right">
        <img src="{{ asset('assets/img/products/munsiyari-rajma.webp') }}" alt="Munsiyari rajma, a traditional hill-grown pulse" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async" width="800" height="800">
        <div class="absolute inset-0 bg-gradient-to-t from-clay-900/95 via-clay-900/45 to-transparent"></div>
        <div class="relative flex h-full min-h-[420px] flex-col justify-end p-8 text-white sm:min-h-[500px] sm:p-10">
            <p class="text-[11px] font-black uppercase tracking-[.2em] text-marigold-300">Made for sharing</p>
            <h2 class="font-display mt-3 text-4xl font-medium leading-tight">{{ $featuredBundle?->name ?? 'Gift the taste of the hills.' }}</h2>
            <p class="mt-3 text-sm leading-6 text-white/80">{{ Str::limit($featuredBundle?->description ?? 'Curated pantry boxes for celebrations, hosts and thoughtful everyday gestures.', 120) }}</p>
            <span class="mt-6 flex items-center justify-between gap-4"><span class="inline-flex items-center gap-2 text-sm font-black">Explore gift boxes <span class="button-arrow" aria-hidden="true">→</span></span>@if($featuredBundle)<span class="rounded-full bg-white/15 px-4 py-2 text-sm font-black backdrop-blur">₹{{ number_format((float) $featuredBundle->price, 0) }}</span>@endif</span>
        </div>
    </a>
</section>

{{-- ───────────── Provenance story (scroll-driven) ───────────── --}}
<section class="bg-brand-950 py-[clamp(4rem,8vw,7.5rem)] text-white" aria-labelledby="story-title" data-story>
    <div class="site-shell">
        <div class="max-w-3xl" data-reveal><p class="eyebrow eyebrow-line !text-marigold-300">The Pahadi approach</p><h2 id="story-title" class="section-title on-dark mt-5">From a terraced field <em>to your kitchen</em>, with nothing hidden along the way.</h2></div>
        <div class="mt-14 grid gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="lg:sticky lg:top-28 lg:h-[min(72vh,640px)]">
                <div class="story-visual relative aspect-[4/5] overflow-hidden rounded-[2rem] bg-brand-900 lg:aspect-auto lg:h-full" aria-hidden="true">
                    <img src="{{ asset('assets/img/editorial/uttarakhand-terrace-farming.webp') }}" alt="" class="is-active h-full w-full object-cover" loading="lazy" decoding="async" data-story-visual>
                    <img src="{{ asset('assets/img/editorial/tea-farmer-hillside.webp') }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async" data-story-visual>
                    <img src="{{ asset('assets/img/products/ringal-basket.webp') }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async" data-story-visual>
                    <span class="absolute inset-0 bg-gradient-to-t from-brand-950/70 via-transparent to-transparent"></span>
                </div>
            </div>
            <ol class="relative pl-16">
                <span class="story-line" aria-hidden="true"><span data-story-progress></span></span>
                @foreach([
                    ['title' => 'Know where it comes from', 'copy' => 'Region and producer details are published for every product whenever they are available — from Munsiyari rajma to Kinnaur walnuts. You always know which valley grew your food.', 'icon' => 'M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z'],
                    ['title' => 'Understand the batch', 'copy' => 'Harvest dates, batch numbers and preparation notes make each choice transparent. Small batches mean the honey you receive tastes of one season, one place.', 'icon' => 'm12 3 8 4-8 4-8-4 8-4Zm-8 9 8 4 8-4'],
                    ['title' => 'Receive it packed with care', 'copy' => 'Every order is checked by hand and packed by our team in Dehradun, then dispatched with tracking you can follow from your account.', 'icon' => 'm5 12 4 4L19 6'],
                ] as $step)
                    <li class="story-step relative flex min-h-[38vh] flex-col justify-center py-8 first:pt-0" data-story-step>
                        <span class="story-marker absolute -left-16 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full bg-brand-800 text-brand-200 lg:top-12 lg:translate-y-0" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg></span>
                        <p class="text-[11px] font-black uppercase tracking-[.2em] text-marigold-300">Step {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="font-display mt-3 text-3xl font-medium sm:text-4xl">{{ $step['title'] }}</h3>
                        <p class="mt-4 max-w-md text-base leading-8 text-white/70">{{ $step['copy'] }}</p>
                    </li>
                @endforeach
                <li class="pt-4"><a href="{{ route('about') }}" class="button-accent" data-magnetic>Discover our sourcing story <span class="button-arrow" aria-hidden="true">→</span></a></li>
            </ol>
        </div>
    </div>
</section>

{{-- ───────────── Testimonials ───────────── --}}
@if($testimonials->isNotEmpty())
<section class="site-shell section-space" aria-labelledby="reviews-title">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-reveal><div><p class="eyebrow eyebrow-line">Verified buyers</p><h2 id="reviews-title" class="section-title mt-4">Kind words from <em>our kitchen table</em></h2></div></div>
    <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-stagger>
        @foreach($testimonials as $review)
            <figure class="testimonial-card hover-lift" data-reveal style="--i: {{ $loop->index }}">
                <span class="quote-mark" aria-hidden="true">“</span>
                <p class="rating-stars mt-2" style="--rating: {{ $review->rating }}" aria-label="{{ $review->rating }} out of 5 stars"></p>
                <blockquote class="mt-4 flex-1 text-base leading-7 text-stone-700">@if($review->title)<strong class="mb-2 block font-display text-xl font-medium text-ink">{{ $review->title }}</strong>@endif{{ Str::limit($review->body, 220) }}</blockquote>
                <figcaption class="mt-6 flex items-center gap-3 border-t border-stone-100 pt-5"><span class="grid h-10 w-10 place-items-center rounded-full bg-brand-900 text-sm font-black text-marigold-300" aria-hidden="true">{{ Str::upper(Str::substr($review->user->name, 0, 1)) }}</span><span><strong class="block text-sm text-ink">{{ $review->user->name }}</strong>@if($review->product)<a href="{{ route('products.show', $review->product->slug) }}" class="text-xs text-brand-700 hover:underline">on {{ $review->product->name }}</a>@endif</span></figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

{{-- ───────────── Rewards ───────────── --}}
<section class="site-shell {{ $testimonials->isEmpty() ? 'pt-[clamp(3.5rem,7vw,6.5rem)]' : '' }}" aria-labelledby="rewards-promo-title">
    <div class="relative overflow-hidden rounded-[2rem] bg-marigold-100" data-reveal="scale">
        <svg class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 text-marigold-200" viewBox="0 0 200 200" fill="none" stroke="currentColor" aria-hidden="true">@for($ring = 1; $ring <= 6; $ring++)<circle cx="100" cy="100" r="{{ $ring * 15 }}" stroke-width="1"/>@endfor</svg>
        <div class="relative grid lg:grid-cols-[1.1fr_.9fr]">
            <div class="px-7 py-12 sm:px-12 sm:py-16">
                <p class="eyebrow eyebrow-line !text-clay-600">Pahadi Roots rewards</p>
                <h2 id="rewards-promo-title" class="section-title mt-5">Share a little goodness. <em>Earn something back.</em></h2>
                <p class="lead mt-6 max-w-lg">Invite a friend with your personal link. After their first qualifying order, you earn <strong class="text-brand-900">{{ config('commerce.loyalty.referrer_reward') }} points</strong> and they receive <strong class="text-brand-900">{{ config('commerce.loyalty.referred_customer_reward') }} points</strong>.</p>
                <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('rewards') }}" class="button-primary" data-magnetic>See how it works <span class="button-arrow" aria-hidden="true">→</span></a><a href="{{ auth()->check() ? route('profile') : route('register') }}" class="button-secondary">{{ auth()->check() ? 'Get my referral link' : 'Create free account' }}</a></div>
                <p class="mt-5 text-xs text-stone-500">No subscription. No joining fee. Rewards are applied automatically.</p>
            </div>
            <div class="relative min-h-[340px] overflow-hidden">
                <img src="{{ asset('assets/img/products/multiflora-honey.webp') }}" alt="Himalayan honey to share with friends and family" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async" width="900" height="900" data-parallax="0.08">
                <div class="absolute inset-0 bg-gradient-to-t from-brand-950/85 via-brand-950/10 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 grid grid-cols-2 gap-3 p-6 sm:p-8">
                    <div class="reward-stat surface-glass rounded-2xl p-5 text-white"><span class="font-display text-4xl text-marigold-300">+<span data-count-to="{{ config('commerce.loyalty.referrer_reward') }}">{{ config('commerce.loyalty.referrer_reward') }}</span></span><p class="mt-1 text-[11px] font-black uppercase tracking-wider">Points for you</p></div>
                    <div class="reward-stat surface-glass rounded-2xl p-5 text-white"><span class="font-display text-4xl text-marigold-300">+<span data-count-to="{{ config('commerce.loyalty.referred_customer_reward') }}">{{ config('commerce.loyalty.referred_customer_reward') }}</span></span><p class="mt-1 text-[11px] font-black uppercase tracking-wider">For your friend</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ───────────── Journal ───────────── --}}
@if($journalPosts->isNotEmpty())
<section class="site-shell section-space" aria-labelledby="journal-title">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between" data-reveal><div><p class="eyebrow eyebrow-line">From the journal</p><h2 id="journal-title" class="section-title mt-4">Stories from <em>the hills</em></h2><p class="lead mt-4 max-w-2xl">Ingredient guides, regional food traditions and practical ways to use your mountain pantry.</p></div><a href="{{ route('blog') }}" class="button-secondary self-start sm:self-auto">Explore the journal <span class="button-arrow" aria-hidden="true">→</span></a></div>
    <div class="mt-10 grid gap-5 md:grid-cols-3" data-reveal-stagger>
        @foreach($journalPosts as $post)
            <article class="journal-card group overflow-hidden border border-stone-200/80 bg-white" data-reveal style="--i: {{ $loop->index }}"><a href="{{ route('blog.show', $post->slug) }}" class="block aspect-[16/11] w-full overflow-hidden bg-stone-100">@if($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $post->image_alt }}" class="h-full w-full object-cover" loading="lazy" decoding="async" width="720" height="495">@endif</a><div class="p-6"><div class="flex justify-between gap-3 text-[10px] font-black uppercase tracking-[.16em] text-brand-600"><span>{{ $post->category }}</span><span class="text-stone-400">{{ $post->reading_time }} min read</span></div><h3 class="font-display mt-3 text-2xl font-medium leading-tight text-brand-950"><a href="{{ route('blog.show', $post->slug) }}" class="hover:text-brand-700">{{ $post->title }}</a></h3><a href="{{ route('blog.show', $post->slug) }}" class="mt-5 inline-flex items-center gap-2 text-sm font-black text-brand-700">Read story <span class="button-arrow" aria-hidden="true">→</span></a></div></article>
        @endforeach
    </div>
</section>
@endif

{{-- ───────────── Delivery check ───────────── --}}
<section class="site-shell {{ $journalPosts->isEmpty() ? 'pt-[clamp(3.5rem,7vw,6.5rem)]' : '' }}" aria-labelledby="delivery-title">
    <div class="relative overflow-hidden rounded-[2rem] bg-white p-8 shadow-[var(--shadow-sm)] ring-1 ring-stone-200/70 sm:p-12" data-reveal>
        <svg class="pointer-events-none absolute bottom-0 right-0 h-40 w-[60%] text-brand-50" viewBox="0 0 600 160" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 160 120 60l70 50 110-90 90 70 80-40 130 110Z"/></svg>
        <div class="relative grid gap-8 lg:grid-cols-[1fr_1fr] lg:items-center">
            <div><p class="eyebrow eyebrow-line">Delivery check</p><h2 id="delivery-title" class="section-title mt-4">See if we <em>deliver to you.</em></h2><p class="lead mt-4 max-w-lg">Enter your six-digit pincode for availability, estimated delivery time and cash-on-delivery eligibility.</p></div>
            <div>
                <form method="POST" action="{{ route('pincode.check') }}" data-pincode-form>@csrf<div class="flex gap-2 rounded-full bg-parchment p-2 ring-1 ring-stone-200"><label for="homePincode" class="sr-only">Delivery pincode</label><input id="homePincode" name="pincode" inputmode="numeric" autocomplete="postal-code" pattern="[1-9][0-9]{5}" maxlength="6" placeholder="Enter pincode" class="min-w-0 flex-1 !rounded-full !border-0 !bg-transparent px-5 text-lg font-bold tracking-[.2em] !shadow-none placeholder:font-medium placeholder:tracking-normal" required><button class="button-primary shrink-0" type="submit" data-pincode-button>Check now</button></div><div data-pincode-result role="status" aria-live="polite" class="mt-3 hidden rounded-2xl border p-4 text-sm"></div></form>
                <p class="mt-3 pl-4 text-xs text-stone-500">We currently dispatch orders from Dehradun, Uttarakhand.</p>
            </div>
        </div>
    </div>
</section>
@endsection
