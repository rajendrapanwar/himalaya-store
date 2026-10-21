@extends('layouts.app')

@section('title', 'Himalayan Food from Uttarakhand | Pahadi Roots')
@section('meta_description', 'Shop traceable mountain honey, herbal teas, pulses, grains, spices and handmade gifts sourced across Uttarakhand and Himachal Pradesh.')
@section('og_image', $heroSlides->first()->image_url)
@section('og_image_alt', $heroSlides->first()->image_alt)
@section('full_bleed', '1')
@section('header_overlay', '1')

@php
    $productTabs = collect([
        ['id' => 'popular', 'label' => 'Popular', 'products' => $featuredProducts],
        ['id' => 'new', 'label' => 'New arrivals', 'products' => $latestProducts->take(8)],
        ['id' => 'sale', 'label' => 'On sale', 'products' => $saleProducts],
    ])->filter(fn ($tab) => $tab['products']->isNotEmpty())->values();
    $fallbackImages = ['editorial/uttarakhand-terrace-farming.webp', 'editorial/misty-tea-mountains.webp', 'editorial/tea-farmer-hillside.webp', 'editorial/rolling-cultivated-hills.webp', 'editorial/darjeeling-tea-garden.webp'];
    $freeDeliveryThreshold = '₹'.number_format((float) config('commerce.free_shipping_threshold'), 0);
    $homeFaqs = [
        ['Where do your products come from?', 'We focus on products connected to Uttarakhand and the wider Himalayan region. Origin, producer, ingredients and batch details are shown whenever the information is available.'],
        ['How long will my order take?', 'Orders are generally prepared within 1–2 business days. Delivery time varies by pincode, courier coverage, weather and road conditions.'],
        ['Which payment methods are supported?', 'Stripe checkout supports eligible UPI options, debit cards and credit cards. Cash on delivery is available for supported pincodes.'],
        ['Are natural variations normal?', 'Yes. Colour, texture, aroma and flavour can vary between harvests and batches. Honey crystallisation is also a natural process.'],
    ];
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
{{-- ═══════════ Hero: layered Himalayan dawn with the seasonal campaign ═══════════ --}}
<section class="landscape landscape-sky min-h-[100svh]" data-landscape data-hero-slider data-hero-delay="7000" aria-roledescription="carousel" aria-label="Featured Pahadi Roots campaigns">
    <span class="landscape-sun" data-landscape-sun aria-hidden="true"></span>
    <span class="landscape-mist top-[38%]" aria-hidden="true"></span>

    {{-- Mountain ridges, far to near. The nearest is snow-coloured and flows into the page. --}}
    <svg class="landscape-layer h-[80vh] min-h-[455px]" data-depth="0.15" viewBox="0 0 1440 700" preserveAspectRatio="none" aria-hidden="true"><path fill="#8b5d60" fill-opacity=".55" d="M0 260 120 190l90 40 120-110 100 90 130-120 130 110 110-60 130 90 130-120 120 100 120-60 140 80v170H0Z"/><path fill="#f6f0e4" fill-opacity=".55" d="M530 120 560 90 592 124 575 118 560 128 545 116ZM1030 130 1060 110 1088 140 1072 134 1060 142 1046 132ZM300 140 330 120 356 146 342 142 330 150 318 140Z"/><rect y="399" width="1440" height="301" fill="#8b5d60" fill-opacity=".55"/></svg>
    <svg class="landscape-layer h-[74vh] min-h-[400px]" data-depth="0.3" viewBox="0 0 1440 700" preserveAspectRatio="none" aria-hidden="true"><path fill="#4a3a45" d="M0 300 140 230l120 50 120-90 140 80 120-70 130 90 130-80 130 70 130-60 140 70 140-50v160H0Z"/><rect y="399" width="1440" height="301" fill="#4a3a45"/></svg>
    <svg class="landscape-layer h-[66vh] min-h-[350px]" data-depth="0.5" viewBox="0 0 1440 700" preserveAspectRatio="none" aria-hidden="true"><path fill="#1c2c26" d="M0 330 110 290l130 50 120-70 120 60 140-50 140 65 120-45 130 50 130-60 130 50 170-40v100H0Z"/><rect y="399" width="1440" height="301" fill="#1c2c26"/></svg>
    <svg class="landscape-layer h-[42vh] min-h-[210px]" data-depth="0.8" viewBox="0 0 1440 700" preserveAspectRatio="none" aria-hidden="true"><path fill="#f6f0e4" d="M0 300c160-60 260-20 420-40s280 40 460 20 320-70 560-20v140H0Z"/><rect y="399" width="1440" height="301" fill="#f6f0e4"/></svg>

    <div class="site-shell relative z-10 grid min-h-[100svh] items-center gap-10 pb-[21vh] pt-[calc(var(--header-height)+var(--ticker-height)+2.5rem)] lg:grid-cols-[1.15fr_.85fr]" data-landscape-fade>
        <div>
            <div class="relative">
                @foreach($heroSlides as $slide)
                    @php
                        $isFirst = $loop->first;
                        $primaryUrl = str_starts_with((string) $slide->primary_url, '/') ? url($slide->primary_url) : $slide->primary_url;
                        $secondaryUrl = str_starts_with((string) $slide->secondary_url, '/') ? url($slide->secondary_url) : $slide->secondary_url;
                    @endphp
                    <article class="hero-copy-slide {{ $isFirst ? 'is-active' : '' }}" data-hero-slide data-hero-copy aria-hidden="{{ $isFirst ? 'false' : 'true' }}" @if(!$isFirst) inert @endif aria-roledescription="slide" aria-label="Slide {{ $loop->iteration }} of {{ $heroSlides->count() }}">
                        @if($slide->eyebrow)<p class="eyebrow text-sun">{{ $slide->eyebrow }}</p>@endif
                        @if($isFirst)<h1 class="display-title mt-6 max-w-[15ch] text-paper !text-[clamp(2.8rem,6vw,5.9rem)]" data-split>{{ $slide->title }}</h1>@else<h2 class="display-title mt-6 max-w-[15ch] text-paper !text-[clamp(2.8rem,6vw,5.9rem)]" data-split>{{ $slide->title }}</h2>@endif
                        @if($slide->description)<p class="lead mt-7 max-w-xl text-paper">{{ $slide->description }}</p>@endif
                        @if($slide->primary_label || $slide->secondary_label)<div class="mt-9 flex flex-wrap gap-3">@if($slide->primary_label && $primaryUrl)<a href="{{ $primaryUrl }}" class="button-accent" data-magnetic>{{ $slide->primary_label }} <span class="button-arrow" aria-hidden="true">→</span></a>@endif @if($slide->secondary_label && $secondaryUrl)<a href="{{ $secondaryUrl }}" class="button-ghost-light">{{ $slide->secondary_label }}</a>@endif</div>@endif
                    </article>
                @endforeach
            </div>
            @if($heroSlides->count() > 1)
                <div class="mt-12 flex max-w-lg items-center gap-4 text-paper">
                    <button type="button" class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-paper/40 transition hover:bg-paper hover:text-night" data-hero-toggle aria-label="Pause slideshow" aria-pressed="false"><svg class="h-4 w-4" data-icon-pause fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg><svg class="hidden h-4 w-4" data-icon-play fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button>
                    <div class="grid flex-1 gap-2" style="grid-template-columns: repeat({{ $heroSlides->count() }}, minmax(0, 1fr))" role="tablist" aria-label="Choose a featured slide">
                        @foreach($heroSlides as $slide)<button type="button" class="group py-3 text-left" data-hero-dot role="tab" aria-label="Show slide {{ $loop->iteration }}: {{ $slide->title }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"><span class="hero-progress {{ $loop->first ? 'is-active' : '' }}" data-hero-progress></span><span class="mt-2 hidden truncate text-[11px] font-bold text-paper/60 transition group-hover:text-paper sm:block">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} · {{ $slide->eyebrow ?: Str::limit($slide->title, 22) }}</span></button>@endforeach
                    </div>
                    <div class="hidden gap-2 sm:flex"><button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-paper/40 transition hover:bg-paper hover:text-night" data-hero-previous aria-label="Previous slide"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg></button><button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-paper/40 transition hover:bg-paper hover:text-night" data-hero-next aria-label="Next slide"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 6 6 6-6 6"/></svg></button></div>
                </div>
            @endif
        </div>

        <div class="relative mx-auto w-[74%] max-w-[390px] lg:mx-0 lg:ml-auto lg:w-full">
            <div class="arch-frame aspect-[3/4] bg-night-2" data-reveal="scale">
                @foreach($heroSlides as $slide)
                    <div class="hero-slide {{ $loop->first ? 'is-active' : '' }}" data-hero-image aria-hidden="true"><img src="{{ $slide->image_url }}" data-image-fallback alt="" class="hero-media" width="880" height="1170" @if($loop->first) fetchpriority="high" @else loading="lazy" decoding="async" @endif></div>
                @endforeach
            </div>
            <x-rotating-badge class="absolute -bottom-8 -left-8 sm:-left-12" size="9rem" text="Single origin • Small batch • Hand packed • " />
            <x-sticker class="absolute -right-4 top-10 rotate-12 text-night sm:-right-8" fill="#c8234e" size="6.5rem"><span class="text-paper">Packed in<br>Dehradun</span></x-sticker>
        </div>
    </div>

    <a href="#home-intro" class="absolute bottom-[9vh] left-1/2 z-20 hidden -translate-x-1/2 flex-col items-center gap-3 text-[10px] font-black uppercase tracking-[.35em] text-paper md:flex" data-cursor="Scroll"><span>Scroll</span><span class="h-12 w-px overflow-hidden bg-paper/25"><span class="block h-full w-full animate-[scroll-line_2.2s_var(--ease-out-expo)_infinite] bg-buransh"></span></span></a>
</section>

{{-- ═══════════ Tilted ticker ═══════════ --}}
<div class="relative z-10 -mt-6 overflow-hidden py-6" aria-label="Why shop with Pahadi Roots">
    <div class="ticker ticker-tilt chapter-buransh py-5" style="--ticker-speed: 45s">
        @foreach([false, true] as $duplicate)
            <ul class="ticker-track" @if($duplicate) aria-hidden="true" @endif>
                @foreach(['Traceable origin', 'Small-batch harvests', 'Packed by hand in Dehradun', 'Free delivery over '.$freeDeliveryThreshold, '30-day returns', 'Cards, UPI & cash on delivery'] as $value)
                    <li class="ticker-item font-display text-3xl font-semibold italic sm:text-5xl" style="font-variation-settings: 'SOFT' 100, 'WONK' 1">{{ $value }}<span class="text-sun not-italic" aria-hidden="true">✺</span></li>
                @endforeach
            </ul>
        @endforeach
    </div>
</div>

{{-- ═══════════ (01) Manifesto + numbers ═══════════ --}}
<section id="home-intro" class="chapter-snow section-space">
    <div class="site-shell">
        <div class="flex items-center justify-between gap-6"><p class="eyebrow">Rooted in the hills</p><span class="section-number">(01)</span></div>
        <p class="manifesto mt-10 max-w-[22ch] sm:max-w-none" data-manifesto>Good food should never feel anonymous. We trace every jar, sack and basket back to the terraced slopes, the forest hives and the hands that tend them — <em>then pack it with care in Dehradun.</em></p>

        <div class="mt-20 grid grid-cols-2 border-t-[1.5px] border-ink lg:grid-cols-4">
            @foreach([
                ['value' => $stats['products'], 'suffix' => '', 'label' => 'Products from mountain producers'],
                ['value' => $stats['collections'], 'suffix' => '', 'label' => 'Curated pantry collections'],
                ['value' => max($stats['regions'], 1), 'suffix' => '+', 'label' => 'Himalayan regions represented'],
                ['value' => 30, 'suffix' => '', 'label' => 'Day returns on eligible orders'],
            ] as $stat)
                <div class="border-b-[1.5px] border-ink py-8 pr-6 {{ $loop->odd ? 'border-r-[1.5px]' : '' }} {{ $loop->index < 3 ? 'lg:border-r-[1.5px]' : '' }} lg:border-b-0 lg:pl-6 lg:first:pl-0">
                    <p class="stat-number"><span data-count-to="{{ $stat['value'] }}">{{ $stat['value'] }}</span><span class="text-buransh">{{ $stat['suffix'] }}</span></p>
                    <p class="mt-3 max-w-[16ch] text-sm font-semibold leading-5 opacity-70">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════ (02) Collections: pinned horizontal gallery ═══════════ --}}
@if($categories->isNotEmpty())
<svg class="ridge text-night" viewBox="0 0 1440 90" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 90V60l120-34 90 24 150-48 110 40 120-26 140 44 100-30 140 30 110-50 120 42 90-20 150 36v82Z"/></svg>
<section class="chapter-night overflow-hidden" data-hscroll aria-labelledby="collections-title">
    <div class="flex min-h-[100svh] flex-col justify-center py-16 lg:py-0">
        <div class="site-shell flex items-end justify-between gap-6">
            <div><p class="eyebrow">Browse the range</p><h2 id="collections-title" class="section-title mt-5" data-split>Shop by <em>collection</em></h2></div>
            <span class="section-number hidden sm:inline">(02)</span>
        </div>
        <div class="hscroll-viewport mt-12 pl-[max(1.25rem,calc((100vw-1360px)/2))]">
            <div class="hscroll-track pr-[max(1.25rem,calc((100vw-1360px)/2))]" data-hscroll-track>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="hscroll-panel group block h-[58vh] min-h-[380px] w-[78vw] sm:w-[46vw] lg:w-[26vw]" data-cursor="Explore">
                        <img src="{{ $category->image ? asset('storage/'.$category->image) : asset('assets/img/'.$fallbackImages[$loop->index % count($fallbackImages)]) }}" data-image-fallback alt="" class="absolute inset-0 -z-10 h-full w-[116%] max-w-none object-cover" data-hscroll-image loading="lazy" decoding="async" width="700" height="900">
                        <span class="absolute inset-0 -z-10 bg-gradient-to-t from-night via-night/20 to-transparent" aria-hidden="true"></span>
                        <span class="absolute left-6 top-6 font-display text-6xl italic text-snow/90" style="font-variation-settings: 'SOFT' 100, 'WONK' 1">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-6">
                            <span><span class="block text-[11px] font-black uppercase tracking-[.2em] text-sun">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span><span class="font-display mt-2 block text-3xl font-semibold leading-none text-snow sm:text-4xl">{{ $category->name }}</span></span>
                            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-snow text-night transition duration-500 group-hover:-rotate-45 group-hover:bg-sun" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
                        </span>
                    </a>
                @endforeach
                <a href="{{ route('products.index') }}" class="hscroll-panel chapter-sun group flex h-[58vh] min-h-[380px] w-[78vw] flex-col justify-between p-8 sm:w-[46vw] lg:w-[26vw]">
                    <x-sticker fill="#c8234e" size="6rem"><span class="text-paper">All {{ $stats['products'] }}</span></x-sticker>
                    <span><span class="font-display block text-5xl font-semibold leading-[.95]">The whole <em class="italic text-buransh-deep">mountain pantry</em></span><span class="mt-6 inline-flex items-center gap-2 text-sm font-black uppercase tracking-[.14em]">Shop everything <span class="button-arrow" aria-hidden="true">→</span></span></span>
                </a>
            </div>
        </div>
        <div class="site-shell mt-10 hidden lg:block"><div class="h-[2px] w-full bg-snow/15"><span class="block h-full origin-left scale-x-0 bg-sun" data-hscroll-bar></span></div></div>
    </div>
</section>
<svg class="ridge rotate-180 text-night" viewBox="0 0 1440 90" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 90V60l120-34 90 24 150-48 110 40 120-26 140 44 100-30 140 30 110-50 120 42 90-20 150 36v82Z"/></svg>
@endif

{{-- ═══════════ Values strip ═══════════ --}}
<section class="site-shell py-16" aria-label="The Pahadi Roots promise">
    <ul class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['title' => 'Named origins', 'copy' => 'Region and producer published for every product we can trace.', 'icon' => 'M12 21s7-5.4 7-11.5a7 7 0 1 0-14 0C5 15.6 12 21 12 21Zm0-8.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z', 'color' => 'bg-buransh text-paper'],
            ['title' => 'Small batches', 'copy' => 'Harvest and batch notes, so each jar tastes of one season.', 'icon' => 'm12 3 8 4-8 4-8-4 8-4Zm-8 9 8 4 8-4M4 17l8 4 8-4', 'color' => 'bg-sun text-night'],
            ['title' => 'Packed by hand', 'copy' => 'Checked, packed and dispatched by our team in Dehradun.', 'icon' => 'M4 10h16v10H4V10Zm-1-5h18v5H3V5Zm9 15V5', 'color' => 'bg-night text-sun'],
            ['title' => 'Free over '.$freeDeliveryThreshold, 'copy' => 'Plus 30-day returns, UPI, cards and cash on delivery.', 'icon' => 'M3 7h11v10H3V7Zm11 3h4l3 3v4h-7v-7ZM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z', 'color' => 'bg-glacier text-night'],
        ] as $value)
            <li class="group flex items-start gap-5" data-reveal style="--i: {{ $loop->index }}">
                <span class="grid h-16 w-16 shrink-0 place-items-center rounded-[1.4rem] {{ $value['color'] }} transition duration-500 group-hover:-rotate-6 group-hover:scale-110"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $value['icon'] }}"/></svg></span>
                <span><strong class="font-display block text-2xl font-semibold">{{ $value['title'] }}</strong><span class="mt-1 block text-sm leading-6 opacity-70">{{ $value['copy'] }}</span></span>
            </li>
        @endforeach
    </ul>
</section>

{{-- ═══════════ (03) Favourites ═══════════ --}}
@if($productTabs->isNotEmpty())
<section class="site-shell pb-[clamp(4rem,9vw,8rem)] pt-8" aria-labelledby="favourites-title">
    <div class="flex flex-col gap-8 md:flex-row md:items-end md:justify-between">
        <div><div class="flex items-center gap-6"><p class="eyebrow">Curated essentials</p><span class="section-number">(03)</span></div><h2 id="favourites-title" class="section-title mt-5" data-split>This season’s <em>favourites</em></h2></div>
        @if($productTabs->count() > 1)
            <div class="tabs self-start md:self-auto" role="tablist" aria-label="Product groups" data-tabs>
                <span class="tab-indicator" aria-hidden="true"></span>
                @foreach($productTabs as $tab)<button type="button" class="tab" role="tab" id="tab-{{ $tab['id'] }}" aria-controls="panel-{{ $tab['id'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $tab['label'] }}</button>@endforeach
            </div>
        @endif
    </div>
    @foreach($productTabs as $tab)
        <div id="panel-{{ $tab['id'] }}" role="tabpanel" aria-labelledby="tab-{{ $tab['id'] }}" class="mt-12 grid grid-cols-2 gap-x-4 gap-y-12 sm:gap-x-6 lg:grid-cols-4" @unless($loop->first) hidden @endunless @if($loop->first) data-reveal-stagger @endif>
            @foreach($tab['products'] as $product)<x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" />@endforeach
        </div>
    @endforeach
    <div class="mt-16 flex justify-center"><a href="{{ route('products.index') }}" class="button-primary" data-magnetic>Shop all {{ $stats['products'] }} products <span class="button-arrow" aria-hidden="true">→</span></a></div>
</section>
@endif

{{-- ═══════════ (04) Where it grows ═══════════ --}}
@if($regions)
<section class="chapter-glacier section-space" aria-labelledby="origins-title">
    <div class="site-shell grid gap-12 lg:grid-cols-[.8fr_1.2fr]">
        <div class="lg:sticky lg:top-32 lg:self-start">
            <div class="flex items-center gap-6"><p class="eyebrow">Where it grows</p><span class="section-number">(04)</span></div>
            <h2 id="origins-title" class="section-title mt-5" data-split>Every product has a <em>place.</em></h2>
            <p class="lead mt-6 max-w-md">From the apple valleys of Kinnaur to the terraces of Garhwal — choose a region and taste what grows there.</p>
            <x-rotating-badge class="mt-10 hidden lg:inline-grid" size="8rem" color="#13201a" center="#d8e8e1" text="Taste the region • Know the maker • " />
        </div>
        <ol data-reveal-list>
            @foreach($regions as $region)
                <li><a href="{{ route('products.index', ['origin' => $region['name']]) }}" class="reveal-row" @if($region['image']) data-preview-src="{{ $region['image'] }}" @endif data-cursor="Taste">
                    <span class="font-display text-lg italic opacity-60">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span><span class="row-title">{{ Str::before($region['name'], ',') }}</span>@if(Str::contains($region['name'], ','))<span class="ml-3 align-middle text-xs font-black uppercase tracking-[.16em] opacity-60">{{ trim(Str::after($region['name'], ',')) }}</span>@endif</span>
                    <span class="flex items-center gap-3 text-sm font-black"><span class="hidden sm:inline">{{ $region['count'] }} {{ Str::plural('product', $region['count']) }}</span><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 12h14m-6-6 6 6-6 6"/></svg></span>
                </a></li>
            @endforeach
        </ol>
    </div>
</section>
@endif

{{-- ═══════════ (05) The Pahadi standard: stacked cards ═══════════ --}}
<section class="site-shell section-space" aria-labelledby="standard-title">
    <div class="flex items-end justify-between gap-6"><div><p class="eyebrow">The Pahadi standard</p><h2 id="standard-title" class="section-title mt-5 max-w-3xl" data-split>From a terraced field to <em>your kitchen.</em> Nothing hidden.</h2></div><span class="section-number hidden sm:inline">(05)</span></div>
    <div class="mt-14 space-y-6 pb-10" data-stack>
        @foreach([
            ['chapter' => 'chapter-night', 'step' => 'Know where it comes from', 'copy' => 'Region and producer details are published whenever they are available — from Munsiyari rajma to Kinnaur walnuts. You always know which valley grew your food.', 'image' => 'assets/img/editorial/uttarakhand-terrace-farming.webp', 'points' => ['Origin region', 'Producer name', 'Pack weight & ingredients']],
            ['chapter' => 'chapter-buransh', 'step' => 'Understand the batch', 'copy' => 'Harvest dates, batch numbers and preparation notes make each choice transparent. Small batches mean one season, one place, one honest flavour.', 'image' => 'assets/img/editorial/tea-farmer-hillside.webp', 'points' => ['Batch number', 'Harvest date', 'Preparation notes']],
            ['chapter' => 'chapter-sun', 'step' => 'Packed by hand in Dehradun', 'copy' => 'Every order is checked and packed by our own team, then dispatched with tracking you can follow from your account.', 'image' => 'assets/img/products/ringal-basket.webp', 'points' => ['Hand-checked', 'Tracked delivery', 'Order updates by email']],
            ['chapter' => 'chapter-glacier', 'step' => 'Delivered with care', 'copy' => 'Free delivery on orders over '.$freeDeliveryThreshold.', 30-day returns on eligible orders, and pay how you like — UPI, cards or cash on delivery.', 'image' => 'assets/img/products/multiflora-honey.webp', 'points' => ['Free over '.$freeDeliveryThreshold, '30-day returns', 'UPI, cards & COD']],
        ] as $card)
            <article class="stack-card {{ $card['chapter'] }} grid overflow-hidden rounded-[2rem] md:grid-cols-[1.1fr_.9fr]" style="--i: {{ $loop->index }}">
                <div class="flex flex-col justify-between gap-10 p-8 sm:p-12">
                    <div class="flex items-center justify-between"><span class="font-display text-7xl italic leading-none opacity-90" style="font-variation-settings: 'SOFT' 100, 'WONK' 1">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="text-[11px] font-black uppercase tracking-[.22em] opacity-70">Step {{ $loop->iteration }} of 4</span></div>
                    <div><h3 class="font-display text-4xl font-semibold leading-[1] sm:text-5xl">{{ $card['step'] }}</h3><p class="mt-5 max-w-lg text-base leading-8 opacity-80">{{ $card['copy'] }}</p>
                    <ul class="mt-7 flex flex-wrap gap-2">@foreach($card['points'] as $point)<li class="inline-flex items-center gap-2 rounded-full border-[1.5px] border-current px-4 py-2 text-xs font-black uppercase tracking-[.1em]"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg>{{ $point }}</li>@endforeach</ul></div>
                </div>
                <div class="relative min-h-[280px] md:min-h-[460px]"><img src="{{ asset($card['image']) }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async"></div>
            </article>
        @endforeach
    </div>
    <div class="flex justify-center"><a href="{{ route('about') }}" class="button-secondary">Read our sourcing story <span class="button-arrow" aria-hidden="true">→</span></a></div>
</section>

{{-- ═══════════ Promotions ═══════════ --}}
<section class="site-shell grid gap-6 pb-[clamp(4rem,9vw,8rem)] lg:grid-cols-[1.25fr_.75fr]" aria-label="Featured promotions">
    <a href="{{ route('products.index', ['sale' => 1]) }}" rel="nofollow" class="chapter-buransh group relative isolate flex min-h-[520px] flex-col justify-end overflow-hidden rounded-[2rem] p-8 sm:p-12" data-cursor="Shop">
        <span class="absolute inset-0 -z-10" data-reveal="clip"><img src="{{ asset('assets/img/products/wild-forest-honey.webp') }}" alt="Wild forest honey from the Himalayan region" class="h-full w-full object-cover opacity-60 mix-blend-luminosity transition duration-[1.4s] group-hover:scale-105" loading="lazy" decoding="async" width="960" height="720"></span>
        <span class="absolute inset-0 -z-10 bg-gradient-to-t from-buransh-deep via-buransh/40 to-transparent" aria-hidden="true"></span>
        <x-sticker class="absolute right-8 top-8" fill="#f5b51b" size="7rem"><span>Limited<br>time</span></x-sticker>
        <h2 class="font-display max-w-lg text-5xl font-semibold leading-[.95] sm:text-6xl">Mountain favourites, at <em class="italic text-sun">friendlier</em> prices.</h2>
        <span class="mt-7 inline-flex items-center gap-3 text-sm font-black uppercase tracking-[.14em]">Shop current offers <span class="button-arrow" aria-hidden="true">→</span></span>
    </a>
    <a href="{{ route('bundles.index') }}" class="chapter-night group relative isolate flex min-h-[520px] flex-col justify-end overflow-hidden rounded-[2rem] p-8 sm:p-10" data-cursor="Gift">
        <span class="absolute inset-0 -z-10" data-reveal="clip"><img src="{{ asset('assets/img/products/munsiyari-rajma.webp') }}" alt="Munsiyari rajma, a traditional hill-grown pulse" class="h-full w-full object-cover opacity-55 transition duration-[1.4s] group-hover:scale-105" loading="lazy" decoding="async" width="800" height="800"></span>
        <span class="absolute inset-0 -z-10 bg-gradient-to-t from-night via-night/50 to-transparent" aria-hidden="true"></span>
        <p class="eyebrow text-sun">Made for sharing</p>
        <h2 class="font-display mt-4 text-4xl font-semibold leading-[1]">{{ $featuredBundle?->name ?? 'Gift the taste of the hills.' }}</h2>
        <p class="mt-3 text-sm leading-6 opacity-75">{{ Str::limit($featuredBundle?->description ?? 'Curated pantry boxes for celebrations, hosts and thoughtful everyday gestures.', 110) }}</p>
        <span class="mt-6 flex items-center justify-between gap-4"><span class="inline-flex items-center gap-2 text-sm font-black uppercase tracking-[.14em]">Explore gift boxes <span class="button-arrow" aria-hidden="true">→</span></span>@if($featuredBundle)<span class="rounded-full bg-sun px-4 py-2 text-sm font-black text-night">₹{{ number_format((float) $featuredBundle->price, 0) }}</span>@endif</span>
    </a>
</section>

{{-- ═══════════ Testimonials ═══════════ --}}
@if($testimonials->isNotEmpty())
<section class="chapter-night overflow-hidden py-[clamp(4rem,9vw,8rem)]" aria-labelledby="reviews-title">
    <div class="site-shell flex items-end justify-between gap-6"><div><p class="eyebrow">Verified buyers</p><h2 id="reviews-title" class="section-title mt-5" data-split>Kind words from <em>our kitchen table</em></h2></div><span class="section-number hidden sm:inline">(06)</span></div>
    <div class="ticker mt-14" style="--ticker-speed: 70s">
        @foreach([false, true] as $duplicate)
            <div class="ticker-track items-stretch" @if($duplicate) aria-hidden="true" @endif>
                @foreach($testimonials as $review)
                    <figure class="testimonial-card w-[min(82vw,420px)] whitespace-normal {{ $loop->even ? 'chapter-buransh' : 'chapter-snow' }}">
                        <span class="quote-mark {{ $loop->even ? 'text-sun' : 'text-buransh' }}" aria-hidden="true">“</span>
                        <p class="rating-stars mt-3" style="--rating: {{ $review->rating }}" aria-label="{{ $review->rating }} out of 5 stars"></p>
                        <blockquote class="mt-4 flex-1 font-display text-xl leading-snug">{{ Str::limit($review->body, 180) }}</blockquote>
                        <figcaption class="mt-6 text-sm"><strong class="block">{{ $review->user->name }}</strong>@if($review->product)<span class="opacity-70">on {{ $review->product->name }}</span>@endif</figcaption>
                    </figure>
                @endforeach
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════ (07) Journal ═══════════ --}}
@if($journalPosts->isNotEmpty())
<section class="site-shell section-space" aria-labelledby="journal-title">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between"><div><div class="flex items-center gap-6"><p class="eyebrow">From the journal</p><span class="section-number">(07)</span></div><h2 id="journal-title" class="section-title mt-5" data-split>Stories from <em>the hills</em></h2></div><a href="{{ route('blog') }}" class="button-secondary self-start sm:self-auto">Explore the journal <span class="button-arrow" aria-hidden="true">→</span></a></div>
    <ol class="mt-12" data-reveal-list>
        @foreach($journalPosts as $post)
            <li><a href="{{ route('blog.show', $post->slug) }}" class="reveal-row" @if($post->image_url) data-preview-src="{{ $post->image_url }}" @endif data-cursor="Read">
                <span class="hidden text-xs font-black uppercase tracking-[.16em] opacity-60 sm:block sm:w-40">{{ $post->category }}</span>
                <span class="row-title">{{ $post->title }}</span>
                <span class="text-sm font-black whitespace-nowrap">{{ $post->reading_time }} min</span>
            </a></li>
        @endforeach
    </ol>
</section>
@endif

{{-- ═══════════ FAQ + delivery check ═══════════ --}}
<section class="site-shell grid gap-10 {{ $journalPosts->isEmpty() ? 'pt-[clamp(4rem,9vw,8rem)]' : '' }} lg:grid-cols-[1.15fr_.85fr]" aria-labelledby="faq-title">
    <div>
        <p class="eyebrow">Good to know</p>
        <h2 id="faq-title" class="section-title mt-5" data-split>Questions, <em>answered</em></h2>
        <div class="mt-10 border-b-[1.5px] border-ink">
            @foreach($homeFaqs as [$question, $answer])
                <details class="details-accordion"><summary>{{ $question }}<span class="plus" aria-hidden="true"></span></summary><p class="accordion-body pb-6 pr-10 text-sm leading-7 opacity-75">{{ $answer }}</p></details>
            @endforeach
        </div>
        <a href="{{ route('faq') }}" class="button-quiet mt-6 !px-0">All frequently asked questions</a>
    </div>
    <div class="chapter-sun relative self-start overflow-hidden rounded-[2rem] p-8 sm:p-10" aria-labelledby="delivery-title">
        <svg class="pointer-events-none absolute -bottom-2 left-0 h-28 w-full text-buransh/20" viewBox="0 0 600 120" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 120 90 50l60 30 110-60 90 50 80-30 170 80Z"/></svg>
        <p class="eyebrow">Delivery check</p>
        <h3 id="delivery-title" class="font-display mt-4 text-4xl font-semibold leading-[1]">Do we deliver <em class="italic text-buransh-deep">to you?</em></h3>
        <p class="mt-4 text-sm leading-6 opacity-80">Enter your six-digit pincode for availability, delivery time and cash-on-delivery eligibility.</p>
        <form method="POST" action="{{ route('pincode.check') }}" class="relative mt-7" data-pincode-form>@csrf<div class="flex gap-2 rounded-full bg-paper p-1.5"><label for="homePincode" class="sr-only">Delivery pincode</label><input id="homePincode" name="pincode" inputmode="numeric" autocomplete="postal-code" pattern="[1-9][0-9]{5}" maxlength="6" placeholder="Pincode" class="min-w-0 flex-1 !min-h-[48px] !rounded-full !border-0 !bg-transparent px-5 text-lg font-black tracking-[.25em] !shadow-none placeholder:font-semibold placeholder:tracking-normal" required><button class="button-primary shrink-0 !min-h-[48px]" type="submit" data-pincode-button>Check</button></div><div data-pincode-result role="status" aria-live="polite" class="mt-3 hidden rounded-2xl border p-4 text-sm"></div></form>
        <p class="relative mt-4 text-xs opacity-70">We dispatch from Dehradun, Uttarakhand.</p>
    </div>
</section>
@endsection
