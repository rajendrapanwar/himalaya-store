@php
    $seoTitle = Str::squish(trim($__env->yieldContent('title', config('seo.default_title'))));
    $seoDescription = Str::limit(Str::squish(strip_tags(trim($__env->yieldContent('meta_description', config('seo.default_description'))))), 170, '');
    $privateRoute = request()->routeIs('admin.*', 'cart.*', 'checkout.*', 'login', 'register', 'password.*', 'verification.*', 'profile', 'orders*', 'wishlist.*', 'notifications.*');
    $filteredListing = request()->routeIs('products.index') && request()->hasAny(['search', 'sort', 'min_price', 'max_price', 'availability', 'sale', 'featured', 'origin', 'rating', 'weight']);
    $robots = trim($__env->yieldContent('robots', $privateRoute ? 'noindex, nofollow' : ($filteredListing ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')));
    $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
    $socialImage = trim($__env->yieldContent('og_image', asset(config('seo.default_social_image'))));
    $socialImageAlt = trim($__env->yieldContent('og_image_alt', 'Pahadi Roots Himalayan pantry collection'));
    $freeDeliveryThreshold = '₹'.number_format((float) config('commerce.free_shipping_threshold'), 0);
    $fullBleed = trim($__env->yieldContent('full_bleed')) !== '';
    $overlayHeader = trim($__env->yieldContent('header_overlay')) !== '';
    $user = auth()->user();
    $menuImages = [
        asset('assets/img/editorial/uttarakhand-terrace-farming.webp'),
        asset('assets/img/products/wild-forest-honey.webp'),
        asset('assets/img/products/ringal-basket.webp'),
        asset('assets/img/editorial/tea-farmer-hillside.webp'),
        asset('assets/img/editorial/misty-tea-mountains.webp'),
        asset('assets/img/products/multiflora-honey.webp'),
        asset('assets/img/editorial/rolling-cultivated-hills.webp'),
    ];
    $menuLinks = [
        ['label' => 'Shop all', 'route' => 'products.index', 'active' => 'products.*'],
        ['label' => 'Gift boxes', 'route' => 'bundles.index', 'active' => 'bundles.*'],
        ['label' => 'Our story', 'route' => 'about', 'active' => 'about'],
        ['label' => 'Journal', 'route' => 'blog', 'active' => 'blog*'],
        ['label' => 'Rewards', 'route' => 'rewards', 'active' => 'rewards'],
        ['label' => 'Contact', 'route' => 'contact', 'active' => 'contact'],
    ];
    $tickerItems = ['Free delivery over '.$freeDeliveryThreshold, 'Packed by hand in Dehradun', 'Named origin on every product', '30-day returns', 'Loyalty points on every order', 'Cards, UPI & cash on delivery'];
@endphp
<!DOCTYPE html>
<html lang="{{ config('seo.language', 'en-IN') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="theme-color" content="#0d1a14">
    <meta name="author" content="{{ config('seo.organization.name') }}">
    @if(config('seo.google_site_verification'))<meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">@endif
    @if(config('seo.bing_site_verification'))<meta name="msvalidate.01" content="{{ config('seo.bing_site_verification') }}">@endif
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="en-IN" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">
    @yield('pagination_links')
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo.png') }}">
    <meta property="og:site_name" content="Pahadi Roots">
    <meta property="og:locale" content="{{ config('seo.locale', 'en_IN') }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta property="og:image:secure_url" content="{{ $socialImage }}">
    <meta property="og:image:alt" content="{{ $socialImageAlt }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <meta name="twitter:image:alt" content="{{ $socialImageAlt }}">
    @if(config('seo.twitter_site'))<meta name="twitter:site" content="{{ config('seo.twitter_site') }}">@endif
    <link rel="alternate" type="application/rss+xml" title="Pahadi Roots Journal" href="{{ route('blog.feed') }}">
    <title>{{ $seoTitle }}</title>
    <script nonce="{{ Vite::cspNonce() }}">document.documentElement.classList.add('js');@if($overlayHeader)try{if(!sessionStorage.getItem('pr-intro')&&!matchMedia('(prefers-reduced-motion: reduce)').matches){document.documentElement.classList.add('show-preloader');sessionStorage.setItem('pr-intro','1');}}catch(e){}@endif</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="storefront min-h-screen antialiased" data-image-fallback-src="{{ asset('assets/img/product-placeholder.svg') }}" data-suggestions-url="{{ route('products.suggestions') }}">
    <a href="#main-content" class="sr-only z-[120] rounded-full bg-paper px-5 py-3 font-bold text-ink shadow-lg focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to content</a>
    <div class="scroll-progress" data-scroll-progress aria-hidden="true"></div>

    @if($overlayHeader)
        <div class="preloader" data-preloader aria-hidden="true">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16" viewBox="0 0 48 48" fill="none"><path class="draw" d="m6 34 11-16 7 9 7-14 11 21" stroke="#f5b51b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path class="draw" d="M8 39c9-3.5 23-3.5 32 0" stroke="#f6f0e4" stroke-width="2" stroke-linecap="round"/></svg>
                <p class="preloader-count mt-4"><span data-preloader-count>0</span><span class="text-buransh">%</span></p>
                <p class="mt-3 text-[11px] font-black uppercase tracking-[.4em] text-snow/60">Pahadi Roots · From the hills</p>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <header class="site-header {{ $overlayHeader ? 'header-on-dark' : '' }}" data-site-header>
        <div class="header-ticker text-[11px] font-black uppercase tracking-[.2em]">
            <div class="ticker h-full items-center" style="--ticker-speed: 50s">
                @foreach([false, true] as $duplicate)
                    <div class="ticker-track" @if($duplicate) aria-hidden="true" @endif>@foreach($tickerItems as $item)<span class="ticker-item">{{ $item }}<span class="text-sun" aria-hidden="true">✺</span></span>@endforeach</div>
                @endforeach
            </div>
        </div>
        <div class="header-bar">
            <div class="site-shell header-inner">
                <div class="flex items-center gap-1 sm:gap-5">
                    <button type="button" class="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="siteMenu" data-drawer-open="siteMenu"><span class="bars" aria-hidden="true"></span><span class="hidden sm:inline">Menu</span></button>
                    <nav class="hidden items-center gap-6 lg:flex" aria-label="Primary navigation">
                        <a href="{{ route('products.index') }}" class="nav-link" @if(request()->routeIs('products.*')) aria-current="page" @endif>Shop</a>
                        <a href="{{ route('bundles.index') }}" class="nav-link" @if(request()->routeIs('bundles.*')) aria-current="page" @endif>Gift boxes</a>
                        <a href="{{ route('blog') }}" class="nav-link" @if(request()->routeIs('blog*')) aria-current="page" @endif>Journal</a>
                    </nav>
                </div>

                <a href="{{ route('home') }}" class="group flex items-center gap-2.5 justify-self-center" aria-label="Pahadi Roots home">
                    <svg class="h-9 w-9 transition duration-700 group-hover:rotate-[-8deg] sm:h-10 sm:w-10" viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect width="48" height="48" rx="24" fill="currentColor"/><path d="m9 32 9-13 6 8 6-12 9 17H9Z" fill="#f5b51b"/><path d="M11 36c8-3 18-3 26 0" stroke="#c8234e" stroke-width="2.4" stroke-linecap="round"/></svg>
                    <span class="font-display whitespace-nowrap text-[1.2rem] font-semibold leading-none tracking-tight sm:text-[1.7rem]">Pahadi Roots</span>
                </a>

                <div class="flex items-center justify-end gap-0.5 sm:gap-1">
                    <button id="searchButton" type="button" class="icon-button" aria-label="Search products" aria-expanded="false" aria-controls="searchPanel" data-search-open><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg></button>
                    <a href="{{ auth()->check() ? route('profile') : route('login') }}" class="icon-button hidden sm:inline-grid" aria-label="{{ auth()->check() ? 'My account' : 'Sign in' }}">@if($user?->avatar_url)<img src="{{ $user->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">@else<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20 21a8 8 0 0 0-16 0m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>@endif</a>
                    <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="icon-button hidden md:inline-grid" aria-label="Wishlist"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></a>
                    @auth<a href="{{ route('notifications.index') }}" class="icon-button hidden md:inline-grid" aria-label="Notifications{{ $unreadNotificationCount ? ', '.$unreadNotificationCount.' unread' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>@if($unreadNotificationCount)<span class="count-badge">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>@endif</a>@endauth
                    <a href="{{ route('cart.index') }}" class="cart-pill ml-1" aria-label="Shopping cart{{ $cartCount ? ', '.$cartCount.' items' : '' }}" aria-controls="cartDrawer" data-drawer-open="cartDrawer" data-cart-trigger><span class="hidden sm:inline">Cart</span><svg class="h-4 w-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 8h14l-1 13H6L5 8Zm3 2V6a4 4 0 0 1 8 0v4"/></svg><span class="cart-count" data-cart-count>{{ $cartCount }}</span></a>
                </div>
            </div>
        </div>
    </header>

    {{-- Full-screen menu --}}
    <div id="siteMenu" class="menu-overlay" role="dialog" aria-modal="true" aria-label="Site menu" data-menu-overlay data-drawer>
        <div class="site-shell flex h-[var(--header-height)] shrink-0 items-center justify-between">
            <a href="{{ route('home') }}" class="font-display text-2xl font-semibold">Pahadi Roots</a>
            <button type="button" class="menu-toggle !bg-snow/10 text-snow" aria-label="Close menu" data-drawer-close><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2.4" d="M6 6l12 12M18 6 6 18"/></svg>Close</button>
        </div>
        <div class="site-shell grid flex-1 gap-10 py-8 lg:grid-cols-[1.4fr_1fr] lg:items-center lg:py-4">
            <nav aria-label="Site menu links">
                <ul>
                    @foreach($menuLinks as $item)
                        <li><a href="{{ route($item['route']) }}" class="menu-link" data-menu-image="{{ $loop->index }}" @if(request()->routeIs($item['active'])) aria-current="page" @endif><span class="inner" style="--i: {{ $loop->index }}"><small>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</small>{{ $item['label'] }}</span></a></li>
                    @endforeach
                </ul>
                <div class="menu-fade mt-10">
                    <p class="text-[11px] font-black uppercase tracking-[.22em] text-snow/50">Collections</p>
                    <div class="mt-4 flex flex-wrap gap-2">@foreach($navigationCategories as $navigationCategory)<a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="chip">{{ $navigationCategory->name }} <small>{{ $navigationCategory->products_count }}</small></a>@endforeach</div>
                </div>
            </nav>
            <div class="menu-fade hidden lg:block">
                <div class="menu-preview aspect-[4/5] w-full max-w-md justify-self-end" data-menu-preview aria-hidden="true">
                    @foreach($menuImages as $image)<img src="{{ $image }}" alt="" class="{{ $loop->first ? 'is-active' : '' }}" loading="lazy">@endforeach
                </div>
            </div>
        </div>
        <div class="site-shell menu-fade flex flex-wrap items-center justify-between gap-4 border-t border-snow/15 py-6 text-sm">
            <div class="flex flex-wrap gap-5">@auth<a href="{{ route('profile') }}" class="link-underline">My account</a><a href="{{ route('orders') }}" class="link-underline">Orders</a><a href="{{ route('wishlist.index') }}" class="link-underline">Wishlist</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="link-underline text-buransh-soft">Sign out</button></form>@else<a href="{{ route('login') }}" class="link-underline">Sign in</a><a href="{{ route('register') }}" class="link-underline">Create account</a>@endauth</div>
            <a href="mailto:support@pahadiroots.in" class="link-underline text-sun">support@pahadiroots.in</a>
        </div>
    </div>

    {{-- Search overlay --}}
    <div id="searchPanel" class="search-overlay" role="dialog" aria-modal="true" aria-label="Search products" data-search-panel>
        <div class="site-shell py-7 sm:py-12">
            <div class="flex items-center justify-between"><p class="eyebrow">Search the pantry</p><button type="button" class="icon-button" aria-label="Close search" data-search-close><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg></button></div>
            <form method="GET" action="{{ route('products.index') }}" class="relative mt-6" role="search" data-search-form>
                <label for="globalSearch" class="sr-only">Search products</label>
                <svg class="pointer-events-none absolute left-0 top-1/2 h-8 w-8 -translate-y-1/2 text-buransh" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2.2" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                <input id="globalSearch" name="search" type="search" autocomplete="off" placeholder="Honey, rajma, Kinnaur…" class="search-input" aria-controls="searchResults" aria-autocomplete="list">
            </form>
            <div class="mt-9 grid gap-10 lg:grid-cols-[1fr_1.6fr]">
                <div><p class="text-[11px] font-black uppercase tracking-[.2em] opacity-60">Popular collections</p><div class="mt-4 flex flex-wrap gap-2">@foreach($navigationCategories->take(8) as $navigationCategory)<a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="chip">{{ $navigationCategory->name }}</a>@endforeach</div></div>
                <div id="searchResults" aria-live="polite" data-search-results><p class="text-[11px] font-black uppercase tracking-[.2em] opacity-60">Start typing for instant results · press / anywhere to search</p></div>
            </div>
        </div>
    </div>

    {{-- Cart drawer --}}
    <aside id="cartDrawer" class="drawer" aria-label="Your cart" data-drawer data-cart-drawer>
        <div class="flex items-center justify-between bg-night px-6 py-5 text-snow"><p class="font-display text-2xl font-semibold">Your basket <span class="ml-1 align-middle font-sans text-sm font-bold text-sun" data-cart-drawer-count>({{ $cartCount }})</span></p><button type="button" class="icon-button" aria-label="Close cart" data-drawer-close><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18"/></svg></button></div>
        <div class="flex min-h-0 flex-1 flex-col" data-cart-drawer-body>@include('partials.cart-drawer-body', ['miniCart' => $miniCart])</div>
    </aside>
    <div class="overlay-backdrop" data-overlay-backdrop></div>

    {{-- Notices --}}
    <div class="toast-region" data-toast-region aria-live="polite">
        @if(session('success'))<div role="status" class="toast" data-dismissible data-autodismiss><span class="toast-icon" aria-hidden="true">✓</span><p class="flex-1 pt-1 font-semibold">{{ session('success') }}</p><button type="button" class="-mr-1 grid h-7 w-7 place-items-center rounded-full text-snow/70 hover:bg-snow/10 hover:text-snow" aria-label="Dismiss message" data-dismiss>×</button><span class="toast-progress" aria-hidden="true"></span></div>@endif
        @if(session('error'))<div role="alert" class="toast is-error" data-dismissible><span class="toast-icon" aria-hidden="true">!</span><p class="flex-1 pt-1 font-semibold">{{ session('error') }}</p><button type="button" class="-mr-1 grid h-7 w-7 place-items-center rounded-full text-snow/70 hover:bg-snow/10 hover:text-snow" aria-label="Dismiss message" data-dismiss>×</button></div>@endif
    </div>

    @unless($overlayHeader)<div class="header-spacer" aria-hidden="true"></div>@endunless
    @if($errors->any())
        <div class="site-shell pt-6"><div role="alert" class="rounded-[1.5rem] bg-buransh px-6 py-5 text-sm text-paper" data-dismissible><div class="flex items-start justify-between gap-4"><strong>Please check the highlighted information.</strong><button type="button" class="text-lg leading-none" aria-label="Dismiss message" data-dismiss>×</button></div><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
    @endif

    <main id="main-content" tabindex="-1" class="{{ $fullBleed ? '' : 'site-shell py-10 sm:py-14' }} focus:outline-none">@yield('content')</main>

    {{-- Footer --}}
    <footer class="chapter-night relative mt-24 overflow-hidden">
        <div class="site-shell pb-10 pt-10">
            <div class="grid gap-10 border-b border-snow/15 pb-14 lg:grid-cols-[1.4fr_1fr] lg:items-end">
                <div>
                    <p class="eyebrow">Pahadi Roots rewards</p>
                    <h2 class="section-title mt-5 text-snow" data-split>Taste the hills. <em>Earn as you shop.</em></h2>
                </div>
                <div class="flex flex-wrap items-center gap-4 lg:justify-end">
                    <a href="{{ auth()->check() ? route('profile') : route('register') }}" class="button-accent" data-magnetic>{{ auth()->check() ? 'View my rewards' : 'Create a free account' }} <span class="button-arrow" aria-hidden="true">→</span></a>
                    <a href="{{ route('rewards') }}" class="button-ghost-light">How rewards work</a>
                </div>
            </div>

            <div class="grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
                <div class="lg:pr-10"><p class="font-display text-3xl font-semibold">Pahadi Roots</p><p class="mt-4 text-sm leading-7 text-snow/70">Traceable mountain produce and thoughtful gifts, sourced with care from Uttarakhand and neighbouring Himalayan communities.</p><p class="mt-4 text-sm text-snow/50">Rajpur Road, Dehradun<br>Uttarakhand 248001</p><div class="mt-7"><p class="text-[10px] font-black uppercase tracking-[.2em] text-sun">Follow our journey</p><div class="mt-3 flex items-center gap-2" aria-label="Pahadi Roots social media">@foreach([
                    ['instagram', 'Instagram', 'M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2Zm0 2A3.8 3.8 0 0 0 4 7.8v8.4A3.8 3.8 0 0 0 7.8 20h8.4a3.8 3.8 0 0 0 3.8-3.8V7.8A3.8 3.8 0 0 0 16.2 4H7.8ZM17.3 5.5a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z'],
                    ['facebook', 'Facebook', 'M13.5 22v-9h3l.5-3.5h-3.5V7.3c0-1 .3-1.8 1.8-1.8H17V2.4c-.5-.1-1.7-.2-3-.2-3 0-5 1.8-5 5V9.5H6V13h3v9h4.5Z'],
                    ['youtube', 'YouTube', 'M21.6 7.2a3 3 0 0 0-2.1-2.1C17.6 4.6 12 4.6 12 4.6s-5.6 0-7.5.5a3 3 0 0 0-2.1 2.1C2 9.1 2 12 2 12s0 2.9.4 4.8a3 3 0 0 0 2.1 2.1c1.9.5 7.5.5 7.5.5s5.6 0 7.5-.5a3 3 0 0 0 2.1-2.1c.4-1.9.4-4.8.4-4.8s0-2.9-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z'],
                    ['pinterest', 'Pinterest', 'M12 2a10 10 0 0 0-3.7 19.3c-.1-1.6 0-3.4.4-5l1.3-5.4s-.3-.7-.3-1.8c0-1.7 1-3 2.2-3 1 0 1.5.8 1.5 1.8 0 1.1-.7 2.8-1.1 4.3-.3 1.3.7 2.4 2 2.4 2.4 0 4.2-2.5 4.2-6.1 0-3.2-2.3-5.4-5.6-5.4-3.8 0-6 2.8-6 5.8 0 1.1.4 2.4 1 3 .1.1.1.2.1.4l-.4 1.6c-.1.5-.5.6-.9.4-1.8-.8-2.9-3.3-2.9-5.3 0-4.3 3.1-8.2 9-8.2 4.7 0 8.4 3.4 8.4 7.9 0 4.7-3 8.5-7.1 8.5-1.4 0-2.7-.7-3.1-1.6l-.9 3.3c-.3 1.2-1.2 2.8-1.8 3.7.9.3 1.8.4 2.7.4A10 10 0 0 0 12 2Z'],
                ] as [$network, $label, $path])@if(config('services.social.'.$network))<a href="{{ config('services.social.'.$network) }}" target="_blank" rel="noopener noreferrer" class="grid h-11 w-11 place-items-center rounded-full border border-snow/20 transition duration-300 hover:-translate-y-1 hover:border-sun hover:bg-sun hover:text-night" aria-label="Follow Pahadi Roots on {{ $label }}"><svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $path }}"/></svg></a>@endif @endforeach<button type="button" data-share data-share-title="Pahadi Roots" data-share-text="Traceable mountain produce and thoughtful gifts from Uttarakhand." data-share-url="{{ route('home') }}" class="grid h-11 w-11 place-items-center rounded-full border border-snow/20 transition duration-300 hover:-translate-y-1 hover:border-sun hover:bg-sun hover:text-night" aria-label="Share Pahadi Roots"><svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.8"/><circle cx="6" cy="12" r="2.5" stroke-width="1.8"/><circle cx="18" cy="19" r="2.5" stroke-width="1.8"/><path stroke-width="1.8" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button></div></div></div>
                <div><h2 class="text-[11px] font-black uppercase tracking-[.2em] text-sun">Shop</h2><ul class="mt-5 space-y-3 text-sm text-snow/80"><li><a href="{{ route('products.index') }}" class="link-underline hover:text-snow">All products</a></li>@foreach($navigationCategories->take(5) as $navigationCategory)<li><a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="link-underline hover:text-snow">{{ $navigationCategory->name }}</a></li>@endforeach<li><a href="{{ route('bundles.index') }}" class="link-underline hover:text-snow">Gift boxes</a></li></ul></div>
                <div><h2 class="text-[11px] font-black uppercase tracking-[.2em] text-sun">Information</h2><ul class="mt-5 space-y-3 text-sm text-snow/80"><li><a href="{{ route('about') }}" class="link-underline hover:text-snow">Our story</a></li><li><a href="{{ route('blog') }}" class="link-underline hover:text-snow">Journal</a></li><li><a href="{{ route('rewards') }}" class="link-underline hover:text-snow">Rewards & referrals</a></li><li><a href="{{ route('faq') }}" class="link-underline hover:text-snow">FAQs</a></li><li><a href="{{ route('shipping.policy') }}" class="link-underline hover:text-snow">Shipping</a></li><li><a href="{{ route('returns.policy') }}" class="link-underline hover:text-snow">Returns</a></li><li><a href="{{ route('privacy.policy') }}" class="link-underline hover:text-snow">Privacy</a></li><li><a href="{{ route('terms') }}" class="link-underline hover:text-snow">Terms</a></li><li><a href="{{ route('editorial.policy') }}" class="link-underline hover:text-snow">Editorial policy</a></li></ul></div>
                <div><h2 class="text-[11px] font-black uppercase tracking-[.2em] text-sun">Customer care</h2><p class="mt-5 text-sm leading-7 text-snow/70">Monday–Saturday, 10:00 AM–6:00 PM</p><a href="mailto:support@pahadiroots.in" class="mt-3 block text-sm font-bold hover:text-sun">support@pahadiroots.in</a><a href="{{ route('contact') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-sun">Contact our team <span class="button-arrow" aria-hidden="true">→</span></a><p class="mt-6 flex items-center gap-2 text-xs text-snow/50"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.7" d="M7 10V7a5 5 0 0 1 10 0v3m2 0H5v11h14V10Z"/></svg>Secure checkout powered by Stripe</p></div>
            </div>

            <div class="flex flex-col gap-4 border-t border-snow/15 pt-7 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-[10px] font-black uppercase tracking-[.2em] text-sun">Payments accepted</p><p class="mt-1 text-xs text-snow/50">Cards and UPI are processed securely at checkout.</p></div>
                <div class="flex flex-wrap items-center gap-2" role="list" aria-label="Accepted payment methods">
                    <span class="payment-mark" role="listitem" aria-label="Visa"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#fff"/><text x="28" y="19" text-anchor="middle" fill="#1434CB" font-family="Arial,sans-serif" font-size="14" font-weight="900" font-style="italic">VISA</text></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="Mastercard"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#fff"/><circle cx="23" cy="14" r="8" fill="#EB001B"/><circle cx="33" cy="14" r="8" fill="#F79E1B"/><path d="M28 7.9a8 8 0 0 1 0 12.2 8 8 0 0 1 0-12.2Z" fill="#FF5F00"/></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="RuPay"><svg viewBox="0 0 64 28" aria-hidden="true"><rect width="64" height="28" rx="4" fill="#fff"/><text x="25" y="18.5" text-anchor="middle" fill="#17457A" font-family="Arial,sans-serif" font-size="12" font-weight="800">RuPay</text><path d="m45 7 6 7-6 7h4l6-7-6-7h-4Z" fill="#F47B20"/><path d="m40 7 6 7-6 7h4l6-7-6-7h-4Z" fill="#14A05A"/></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="American Express"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#2E77BC"/><text x="28" y="12" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="7" font-weight="900">AMERICAN</text><text x="28" y="21" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="8" font-weight="900">EXPRESS</text></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="UPI"><svg viewBox="0 0 60 28" aria-hidden="true"><rect width="60" height="28" rx="4" fill="#fff"/><text x="23" y="19" text-anchor="middle" fill="#222" font-family="Arial,sans-serif" font-size="14" font-weight="900" font-style="italic">UPI</text><path d="m39 6 6 8-6 8h4l6-8-6-8h-4Z" fill="#E87522"/><path d="m44 6 6 8-6 8h4l6-8-6-8h-4Z" fill="#169B62"/></svg></span>
                </div>
            </div>

            <p class="font-display pointer-events-none mt-12 select-none whitespace-nowrap text-center text-[clamp(3.6rem,16.5vw,16rem)] font-semibold leading-[.78] tracking-[-.055em] text-snow/[.07]" aria-hidden="true">Pahadi Roots</p>
            <div class="mt-8 grid gap-3 border-t border-snow/15 pt-6 text-xs text-snow/50 sm:grid-cols-3 sm:items-center"><p>© {{ date('Y') }} Pahadi Roots. All rights reserved.</p><p class="sm:text-center">Made for the hills, packed in Dehradun.</p><p class="sm:text-right">Made with love ❤️ by <span class="font-bold text-snow">Rajendra Panwar</span></p></div>
        </div>
    </footer>

    <button type="button" class="back-to-top" aria-label="Back to top" data-back-to-top><svg class="ring h-12 w-12" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="none" stroke="#c8234e" stroke-width="2.5" stroke-linecap="round"/></svg><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 15 6-6 6 6"/></svg></button>
    <a href="https://wa.me/{{ config('commerce.whatsapp_number', '919876543210') }}?text={{ urlencode('Hello Pahadi Roots, I need help with an order.') }}" target="_blank" rel="noopener noreferrer" class="whatsapp-fab group fixed bottom-5 right-5 z-40 grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-[0_14px_30px_-10px_rgb(37_211_102/.7)] transition duration-300 hover:-translate-y-1 hover:scale-105" aria-label="Chat with Pahadi Roots on WhatsApp"><svg class="relative h-7 w-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2A9.84 9.84 0 0 0 3.6 16.9L2 22l5.25-1.53A9.96 9.96 0 1 0 12.04 2Zm5.82 14.08c-.24.68-1.4 1.3-1.93 1.37-.5.06-1.12.08-1.81-.14-.42-.13-.96-.31-1.65-.61-2.9-1.26-4.8-4.2-4.94-4.39-.14-.19-1.18-1.57-1.18-3 0-1.44.74-2.14 1.01-2.43.27-.3.6-.37.8-.37h.57c.18 0 .43-.07.67.51.24.58.82 2 .9 2.14.07.14.12.31.02.5-.1.2-.14.32-.29.49-.14.17-.3.38-.43.51-.14.15-.29.3-.12.59.17.29.75 1.24 1.61 2 .1.09 1.75 1.54 3.22 2.14.29.12.52.1.71-.12.2-.21.84-.98 1.06-1.32.22-.34.44-.29.74-.17.31.12 1.94.92 2.27 1.09.34.17.56.25.65.39.08.15.08.83-.16 1.51Z"/></svg></a>

    @stack('scripts')
</body>
</html>
