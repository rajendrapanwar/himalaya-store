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
    // One definition, so the preload below and the hero image always agree.
    $heroSizes = '(min-width: 1024px) 390px, (min-width: 640px) 74vw, 60vw';
    $heroSrcset = app(\App\Services\ImageVariants::class)->srcset($heroSlides->first()->image_path);
    $homeFaqs = [
        ['Where do your products come from?', 'We focus on products connected to Uttarakhand and the wider Himalayan region. Origin, producer, ingredients and batch details are shown whenever the information is available.'],
        ['How long will my order take?', 'Orders are generally prepared within 1–2 business days. Delivery time varies by pincode, courier coverage, weather and road conditions.'],
        ['Which payment methods are supported?', 'Stripe checkout supports eligible UPI options, debit cards and credit cards. Cash on delivery is available for supported pincodes.'],
        ['Are natural variations normal?', 'Yes. Colour, texture, aroma and flavour can vary between harvests and batches. Honey crystallisation is also a natural process.'],
    ];
@endphp

@push('head')
<link rel="preload" as="image" href="{{ $heroSlides->first()->image_url }}" @if($heroSrcset) imagesrcset="{{ $heroSrcset }}" imagesizes="{{ $heroSizes }}" @endif fetchpriority="high">
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
{{-- ═══════════ Hero: the seasonal campaign ═══════════ --}}
<section class="landscape lg:min-h-[100svh]" data-landscape data-hero-slider data-hero-delay="7000" aria-roledescription="carousel" aria-label="Featured Pahadi Roots campaigns">
    <div class="site-shell relative z-10 grid items-center gap-8 pb-12 pt-[calc(var(--header-height)+var(--ticker-height)+2rem)] lg:min-h-[100svh] lg:gap-10 lg:pb-20 lg:grid-cols-[1.15fr_.85fr]">
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

        <div class="relative mx-auto w-[60%] max-w-[390px] sm:w-[74%] lg:mx-0 lg:ml-auto lg:w-full">
            <div class="arch-frame aspect-[3/4] bg-night-2" data-reveal="scale">
                @foreach($heroSlides as $slide)
                    <div class="hero-slide {{ $loop->first ? 'is-active' : '' }}" data-hero-image aria-hidden="true"><x-responsive-image :path="$slide->image_path" :sizes="$heroSizes" data-image-fallback alt="" class="hero-media" width="880" height="1170" :fetchpriority="$loop->first ? 'high' : null" :loading="$loop->first ? null : 'lazy'" :decoding="$loop->first ? null : 'async'" /></div>
                @endforeach
            </div>
            <x-rotating-badge class="absolute -bottom-8 -left-8 max-sm:hidden sm:-left-12" size="9rem" text="Single origin • Small batch • Hand packed • " />
            <x-sticker class="absolute -right-4 top-10 rotate-12 text-night sm:-right-8" fill="#2d5a3f" size="6.5rem"><span class="text-paper">Packed in<br>Dehradun</span></x-sticker>
        </div>
    </div>
</section>

{{-- ═══════════ Why Pahadi Roots: one compact strip ═══════════ --}}
<section class="border-b border-[color:var(--border)] bg-paper" aria-label="The Pahadi Roots promise">
    <ul class="site-shell grid grid-cols-2 gap-x-4 gap-y-5 py-7 lg:grid-cols-4 lg:gap-x-8 lg:py-10">
        @foreach([
            ['title' => 'Named origins', 'copy' => 'Region and producer shown for every product we can trace.', 'icon' => 'M12 21s7-5.4 7-11.5a7 7 0 1 0-14 0C5 15.6 12 21 12 21Zm0-8.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
            ['title' => 'Small batches', 'copy' => 'Batch and harvest details wherever we have them.', 'icon' => 'm12 3 8 4-8 4-8-4 8-4Zm-8 9 8 4 8-4M4 17l8 4 8-4'],
            ['title' => 'Packed by hand', 'copy' => 'Checked and dispatched by our team in Dehradun.', 'icon' => 'M4 10h16v10H4V10Zm-1-5h18v5H3V5Zm9 15V5'],
            ['title' => 'Free delivery over '.$freeDeliveryThreshold, 'copy' => '30-day returns. Pay by UPI, card or cash on delivery.', 'icon' => 'M3 7h11v10H3V7Zm11 3h4l3 3v4h-7v-7ZM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z'],
        ] as $value)
            <li class="flex items-center gap-3 sm:items-start sm:gap-4">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-night text-sun" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $value['icon'] }}"/></svg></span>
                <span><strong class="block text-sm font-extrabold leading-5 text-ink">{{ $value['title'] }}</strong><span class="mt-1 hidden text-[13px] leading-5 text-stone-600 sm:block">{{ $value['copy'] }}</span></span>
            </li>
        @endforeach
    </ul>
</section>

{{-- ═══════════ Bestsellers ═══════════ --}}
@if($productTabs->isNotEmpty())
<section class="site-shell section-space" aria-labelledby="favourites-title">
    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
        <div><p class="eyebrow">Curated essentials</p><h2 id="favourites-title" class="section-title mt-4" data-split>This season’s <em>favourites</em></h2></div>
        @if($productTabs->count() > 1)
            <div class="tabs self-start md:self-auto" role="tablist" aria-label="Product groups" data-tabs>
                <span class="tab-indicator" aria-hidden="true"></span>
                @foreach($productTabs as $tab)<button type="button" class="tab" role="tab" id="tab-{{ $tab['id'] }}" aria-controls="panel-{{ $tab['id'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $tab['label'] }}</button>@endforeach
            </div>
        @endif
    </div>
    @foreach($productTabs as $tab)
        {{-- Phones show the first four so the page stays short; the button below leads to the rest. --}}
        <div id="panel-{{ $tab['id'] }}" role="tabpanel" aria-labelledby="tab-{{ $tab['id'] }}" class="mt-10 grid grid-cols-2 gap-x-4 gap-y-10 sm:gap-x-6 lg:grid-cols-4 max-lg:[&>*:nth-child(n+5)]:hidden" @unless($loop->first) hidden @endunless>
            @foreach($tab['products'] as $product)<x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" />@endforeach
        </div>
    @endforeach
    <div class="mt-12 flex justify-center"><a href="{{ route('products.index') }}" class="button-primary">Shop all {{ $stats['products'] }} products <span class="button-arrow" aria-hidden="true">→</span></a></div>
</section>
@endif

{{-- ═══════════ Collections: a plain grid, no scroll-jacking ═══════════ --}}
@if($categories->isNotEmpty())
<section class="chapter-night section-space" aria-labelledby="collections-title">
    <div class="site-shell">
        <div><p class="eyebrow">Browse the range</p><h2 id="collections-title" class="section-title mt-4" data-split>Shop by <em>collection</em></h2></div>
        <ul class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
            @foreach($categories as $category)
                <li><a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-tile group">
                    <x-responsive-image :path="$category->image ?: 'assets/img/'.$fallbackImages[$loop->index % count($fallbackImages)]" sizes="(min-width: 1024px) 19vw, (min-width: 640px) 31vw, 46vw" data-image-fallback alt="" loading="lazy" decoding="async" width="480" height="600" />
                    <span class="category-tile-label"><span class="font-display block text-lg font-semibold leading-tight sm:text-xl">{{ $category->name }}</span><span class="mt-1 block text-[11px] font-bold uppercase tracking-[.14em] text-snow/75">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span></span>
                </a></li>
            @endforeach
            <li><a href="{{ route('products.index') }}" class="category-tile category-tile-all group">
                <span class="category-tile-label"><span class="font-display block text-xl font-semibold leading-[1.05] sm:text-2xl">All {{ $stats['products'] }} products</span><span class="mt-2 inline-flex items-center gap-2 text-[11px] font-black uppercase tracking-[.14em] sm:mt-3"><span class="max-sm:sr-only">Shop everything</span> <span class="button-arrow" aria-hidden="true">→</span></span></span>
            </a></li>
        </ul>
        @if($regions)
            <div class="mt-10 flex flex-wrap items-center gap-2 border-t border-snow/15 pt-8">
                <h3 class="mr-2 text-[11px] font-black uppercase tracking-[.2em] text-snow/70">Or shop by origin</h3>
                @foreach($regions as $region)<a href="{{ route('products.index', ['origin' => $region['name']]) }}" class="chip chip-light">{{ Str::before($region['name'], ',') }} <small>{{ $region['count'] }}</small></a>@endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ═══════════ Offers and gift boxes ═══════════ --}}
<section class="site-shell section-space grid gap-5 {{ $saleProducts->isNotEmpty() ? 'lg:grid-cols-2' : '' }}" aria-label="Featured promotions">
    @if($saleProducts->isNotEmpty())
        <a href="{{ route('products.index', ['sale' => 1]) }}" rel="nofollow" class="promo-card chapter-night group">
            <x-responsive-image :path="$saleProducts->first()->images->sortByDesc('is_main')->first()?->image ?? 'assets/img/products/wild-forest-honey.webp'" sizes="(min-width: 1024px) 50vw, 100vw" data-image-fallback alt="" loading="lazy" decoding="async" width="960" height="720" />
            <p class="eyebrow">On sale now</p>
            <h2 class="font-display mt-4 max-w-md text-4xl font-semibold leading-[1] sm:text-5xl">Mountain favourites at friendlier prices.</h2>
            <span class="mt-6 inline-flex items-center gap-2 text-sm font-black uppercase tracking-[.14em]">Shop current offers <span class="button-arrow" aria-hidden="true">→</span></span>
        </a>
    @endif
    <a href="{{ route('bundles.index') }}" class="promo-card chapter-night group">
        <x-responsive-image :path="$featuredBundle?->image ?: 'assets/img/products/munsiyari-rajma.webp'" sizes="(min-width: 1024px) 50vw, 100vw" data-image-fallback alt="" loading="lazy" decoding="async" width="800" height="800" />
        <p class="eyebrow">Gift boxes</p>
        <h2 class="font-display mt-4 max-w-md text-4xl font-semibold leading-[1] sm:text-5xl">{{ $featuredBundle?->name ?? 'Gift the taste of the hills.' }}</h2>
        <p class="mt-3 max-w-md text-sm leading-6 text-snow/80">@if($featuredBundle && $featuredBundle->products->isNotEmpty()){{ $featuredBundle->products->count() }} hill favourites in one ready-made box{{ $featuredBundle->savings > 0 ? ', ₹'.number_format($featuredBundle->savings, 0).' less than buying them separately.' : '.' }}@else Ready-made sets of hill favourites, priced together for less than buying each item.@endif</p>
        <span class="mt-6 flex flex-wrap items-center gap-4"><span class="inline-flex items-center gap-2 text-sm font-black uppercase tracking-[.14em]">Explore gift boxes <span class="button-arrow" aria-hidden="true">→</span></span>@if($featuredBundle)<span class="rounded-full bg-sun px-4 py-2 text-sm font-black text-night">₹{{ number_format((float) $featuredBundle->price, 0) }}</span>@endif</span>
    </a>
</section>

{{-- ═══════════ Reviews: each one shown once ═══════════ --}}
@if($testimonials->isNotEmpty())
@php
    $shownReviews = $testimonials->take(3);
    $reviewColumns = [1 => 'max-w-xl', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3'][$shownReviews->count()];
@endphp
<section class="bg-paper section-space" aria-labelledby="reviews-title">
    <div class="site-shell">
        <p class="eyebrow">Customer reviews</p>
        <h2 id="reviews-title" class="section-title mt-4" data-split>What customers <em>say</em></h2>
        <div class="mt-10 grid gap-5 {{ $reviewColumns }}">
            @foreach($shownReviews as $review)
                <figure class="surface-card flex flex-col p-7">
                    <p class="rating-stars text-base" style="--rating: {{ $review->rating }}" aria-label="{{ $review->rating }} out of 5 stars"></p>
                    <blockquote class="mt-4 flex-1 font-display text-xl leading-snug text-ink">“{{ Str::limit($review->body, 180) }}”</blockquote>
                    <figcaption class="mt-6 text-sm text-stone-600"><strong class="block text-ink">{{ $review->user->name }}</strong>@if($review->product)<a href="{{ route('products.show', $review->product->slug) }}" class="link-underline">on {{ $review->product->name }}</a>@endif</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════ Journal ═══════════ --}}
@if($journalPosts->isNotEmpty())
<section class="site-shell section-space" aria-labelledby="journal-title">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between"><div><p class="eyebrow">From the journal</p><h2 id="journal-title" class="section-title mt-4" data-split>Stories from <em>the hills</em></h2></div><a href="{{ route('blog') }}" class="button-secondary self-start sm:self-auto">Explore the journal <span class="button-arrow" aria-hidden="true">→</span></a></div>
    <ol class="mt-10" data-reveal-list>
        @foreach($journalPosts as $post)
            <li><a href="{{ route('blog.show', $post->slug) }}" class="reveal-row" @if($post->image_url) data-preview-src="{{ $post->image_url }}" @endif>
                <span class="hidden text-xs font-black uppercase tracking-[.16em] opacity-70 sm:block sm:w-40">{{ $post->category }}</span>
                <span class="row-title">{{ $post->title }}</span>
                <span class="text-sm font-black whitespace-nowrap">{{ $post->reading_time }} min</span>
            </a></li>
        @endforeach
    </ol>
</section>
@endif

{{-- ═══════════ FAQ + delivery check ═══════════ --}}
<section class="site-shell grid gap-10 {{ $journalPosts->isEmpty() ? 'pt-[clamp(4rem,9vw,8rem)]' : '' }} lg:grid-cols-[1.15fr_.85fr]" aria-labelledby="faq-title">
    <div class="min-w-0">
        <p class="eyebrow">Good to know</p>
        <h2 id="faq-title" class="section-title mt-4" data-split>Questions, <em>answered</em></h2>
        <div class="mt-8 border-b-[1.5px] border-ink">
            @foreach($homeFaqs as [$question, $answer])
                <details class="details-accordion"><summary>{{ $question }}<span class="plus" aria-hidden="true"></span></summary><p class="accordion-body pb-6 pr-10 text-sm leading-7 opacity-75">{{ $answer }}</p></details>
            @endforeach
        </div>
        <a href="{{ route('faq') }}" class="button-quiet mt-6 !px-0">All frequently asked questions</a>
    </div>
    <div class="chapter-night min-w-0 self-start overflow-hidden rounded-[2rem] p-7 sm:p-10" aria-labelledby="delivery-title">
        <p class="eyebrow">Delivery check</p>
        <h3 id="delivery-title" class="font-display mt-4 text-4xl font-semibold leading-[1]">Do we deliver to you?</h3>
        <p class="mt-4 text-sm leading-6 text-snow/80">Enter your six-digit pincode for availability, delivery time and cash-on-delivery eligibility.</p>
        <form method="POST" action="{{ route('pincode.check') }}" class="mt-7" data-pincode-form>@csrf<div class="flex gap-2 rounded-full bg-paper p-1.5"><label for="homePincode" class="sr-only">Delivery pincode</label><input id="homePincode" name="pincode" inputmode="numeric" autocomplete="postal-code" pattern="[1-9][0-9]{5}" maxlength="6" placeholder="Pincode" class="min-w-0 flex-1 !min-h-[48px] !rounded-full !border-0 !bg-transparent px-5 text-lg font-black tracking-[.25em] text-ink !shadow-none placeholder:font-semibold placeholder:tracking-normal" required><button class="button-accent shrink-0 !min-h-[48px]" type="submit" data-pincode-button>Check</button></div><div data-pincode-result role="status" aria-live="polite" class="mt-3 hidden rounded-2xl border p-4 text-sm"></div></form>
        <p class="mt-4 text-xs text-snow/70">We dispatch from Dehradun, Uttarakhand.</p>
    </div>
</section>
@endsection
