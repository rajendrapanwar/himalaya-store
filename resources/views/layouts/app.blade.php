@php
    $seoTitle = Str::squish(trim($__env->yieldContent('title', config('seo.default_title'))));
    $seoDescription = Str::limit(Str::squish(strip_tags(trim($__env->yieldContent('meta_description', config('seo.default_description'))))), 170, '');
    $privateRoute = request()->routeIs('admin.*', 'cart.*', 'checkout.*', 'login', 'register', 'password.*', 'verification.*', 'profile', 'orders*', 'wishlist.*', 'notifications.*');
    $filteredListing = request()->routeIs('products.index') && request()->hasAny(['search', 'sort', 'min_price', 'max_price', 'availability', 'sale', 'featured', 'origin', 'rating', 'weight']);
    $robots = trim($__env->yieldContent('robots', $privateRoute ? 'noindex, nofollow' : ($filteredListing ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')));
    $canonicalUrl = trim($__env->yieldContent('canonical', request()->url()));
    $socialImage = trim($__env->yieldContent('og_image', asset(config('seo.default_social_image'))));
    $socialImageAlt = trim($__env->yieldContent('og_image_alt', 'Pahadi Roots Himalayan pantry collection'));
    $freeDeliveryThreshold = '₹'.number_format((float) config('commerce.free_shipping_threshold'), 0);
    $fullBleed = trim($__env->yieldContent('full_bleed')) !== '';
    $navigation = [
        ['label' => 'Gift boxes', 'route' => 'bundles.index', 'active' => 'bundles.*'],
        ['label' => 'Our story', 'route' => 'about', 'active' => 'about'],
        ['label' => 'Journal', 'route' => 'blog', 'active' => 'blog*'],
        ['label' => 'Rewards', 'route' => 'rewards', 'active' => 'rewards'],
        ['label' => 'Contact', 'route' => 'contact', 'active' => 'contact'],
    ];
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="{{ config('seo.language', 'en-IN') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="theme-color" content="#0e2119">
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
    <script nonce="{{ Vite::cspNonce() }}">document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="storefront min-h-screen antialiased" data-image-fallback-src="{{ asset('assets/img/product-placeholder.svg') }}" data-suggestions-url="{{ route('products.suggestions') }}">
    <a href="#main-content" class="sr-only z-[100] rounded-full bg-white px-5 py-3 font-bold text-brand-900 shadow-lg focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to content</a>
    <div class="scroll-progress" data-scroll-progress aria-hidden="true"></div>

    {{-- Announcement bar --}}
    <div class="announcement-bar text-[11px] font-semibold sm:text-xs">
        <div class="site-shell flex items-center justify-between gap-4">
            <p class="hidden items-center gap-2 text-brand-200 lg:flex"><svg class="h-3.5 w-3.5 text-marigold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>Rooted in Uttarakhand</p>
            <div class="announcement-rotator flex-1 lg:flex-none" data-rotator aria-live="off">
                <a href="{{ route('products.index') }}" class="is-active font-bold text-marigold-300 hover:text-marigold-200"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h11v10H3V7Zm11 3h4l3 3v4h-7v-7ZM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg><span>Free delivery on orders over {{ $freeDeliveryThreshold }}</span></a>
                <a href="{{ route('returns.policy') }}" class="hover:text-white"><svg class="h-3.5 w-3.5 text-marigold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 12a8 8 0 0 1 14-5.3L20 9M20 4v5h-5M20 12a8 8 0 0 1-14 5.3L4 15m0 5v-5h5"/></svg><span>30-day returns on eligible orders</span></a>
                <a href="{{ route('rewards') }}" class="hover:text-white"><svg class="h-3.5 w-3.5 text-marigold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7L12 3Z"/></svg><span>Earn loyalty points on every order</span></a>
            </div>
            <div class="hidden items-center gap-5 text-brand-200 lg:flex"><a href="{{ route('faq') }}" class="hover:text-white">Help</a><a href="{{ auth()->check() ? route('orders') : route('login') }}" class="hover:text-white">Track order</a></div>
        </div>
    </div>

    {{-- Header --}}
    <header class="site-header" data-site-header>
        <div class="site-header-bar">
            <div class="site-shell site-header-inner">
                <div class="flex items-center gap-2">
                    <button id="mobileMenuButton" type="button" class="icon-button -ml-2 lg:!hidden" aria-label="Open navigation" aria-expanded="false" aria-controls="mobileMenu" data-drawer-open="mobileMenu"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M4 7h16M4 12h10M4 17h16"/></svg></button>
                    <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-2.5" aria-label="Pahadi Roots home">
                        <svg class="h-10 w-10 transition duration-500 group-hover:-rotate-6 sm:h-11 sm:w-11" viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect width="48" height="48" rx="14" fill="#0e2119"/><path d="m8 31 9-13 6 8 6-12 11 17H8Z" fill="#F8F4EB"/><path d="m13 31 7-7 4 5 6-8 7 10H13Z" fill="#D78A18"/><path d="M10 35c8-3 20-3 28 0" stroke="#F8F4EB" stroke-width="2" stroke-linecap="round"/></svg>
                        <span class="leading-none"><strong class="font-display block text-xl font-semibold tracking-tight text-brand-950 sm:text-2xl">Pahadi Roots</strong><small class="mt-1 hidden text-[9px] font-extrabold uppercase tracking-[.24em] text-brand-600 sm:block">From the hills</small></span>
                    </a>
                </div>

                <nav class="hidden items-center lg:flex" aria-label="Primary navigation">
                    <div class="site-header-group relative" data-menu-group>
                        <div class="flex items-center">
                            <a href="{{ route('products.index') }}" class="nav-link !pr-1" @if(request()->routeIs('products.*')) aria-current="page" @endif>Shop</a>
                            <button type="button" class="grid h-9 w-7 place-items-center rounded-full text-stone-600 hover:text-brand-900" aria-label="Open shop menu" aria-expanded="false" data-menu-button><svg class="menu-chevron h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button>
                        </div>
                        <div class="site-header-menu absolute -left-6 top-full mt-3 w-[min(860px,calc(100vw-3rem))] rounded-[1.75rem] border border-stone-200/70 bg-white p-3 shadow-[0_40px_80px_-30px_rgb(14_33_25/.4)]" data-menu>
                            <div class="grid gap-3 md:grid-cols-[1.45fr_1fr]">
                                <div class="p-3">
                                    <div class="flex items-center justify-between px-2"><p class="eyebrow !text-[10px]">Shop by collection</p><a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-950">View all products →</a></div>
                                    <div class="mt-3 grid gap-1 sm:grid-cols-2">
                                        @foreach($navigationCategories as $navigationCategory)
                                            <a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="mega-link">
                                                @if($navigationCategory->image)<img src="{{ asset('storage/'.$navigationCategory->image) }}" alt="" loading="lazy" width="48" height="48" data-image-fallback>@else<span class="mega-thumb grid place-items-center bg-brand-50 text-brand-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/></svg></span>@endif
                                                <span class="min-w-0"><strong class="block truncate text-sm font-bold text-ink">{{ $navigationCategory->name }}</strong><small class="text-xs text-stone-500">{{ $navigationCategory->products_count }} {{ Str::plural('product', $navigationCategory->products_count) }}</small></span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="grid gap-3">
                                    <a href="{{ route('bundles.index') }}" class="group relative min-h-[150px] overflow-hidden rounded-[1.35rem] bg-brand-950 p-5 text-white">
                                        <img src="{{ asset('assets/img/products/ringal-basket.webp') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-1000 group-hover:scale-110" loading="lazy" width="400" height="300">
                                        <span class="absolute inset-0 bg-gradient-to-t from-brand-950/90 to-transparent"></span>
                                        <span class="relative flex h-full flex-col justify-end"><small class="text-[10px] font-black uppercase tracking-[.2em] text-marigold-300">Ready to gift</small><strong class="font-display mt-1 text-2xl font-medium">Gift boxes & bundles</strong><span class="mt-2 text-xs font-bold">Explore <span class="button-arrow" aria-hidden="true">→</span></span></span>
                                    </a>
                                    <a href="{{ route('products.index', ['sale' => 1]) }}" rel="nofollow" class="group flex items-center justify-between gap-4 rounded-[1.35rem] bg-marigold-100 p-5 text-brand-950">
                                        <span><small class="text-[10px] font-black uppercase tracking-[.2em] text-clay-600">Limited time</small><strong class="font-display mt-1 block text-xl font-medium">Mountain favourites on sale</strong></span>
                                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-white text-lg transition group-hover:-rotate-45" aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @foreach($navigation as $item)
                        <a href="{{ route($item['route']) }}" class="nav-link" @if(request()->routeIs($item['active'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-0.5 sm:gap-1">
                    <button id="searchButton" type="button" class="icon-button" aria-label="Search products" aria-expanded="false" aria-controls="searchPanel" data-search-open><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg></button>
                    @auth<a href="{{ route('notifications.index') }}" class="icon-button hidden sm:inline-grid" aria-label="Notifications{{ $unreadNotificationCount ? ', '.$unreadNotificationCount.' unread' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>@if($unreadNotificationCount)<span class="count-badge">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>@endif</a>@endauth
                    <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="icon-button hidden sm:inline-grid" aria-label="Wishlist"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></a>
                    <div class="site-header-group relative hidden sm:block" data-menu-group>
                        <button type="button" class="icon-button overflow-hidden" aria-label="Open account menu" aria-expanded="false" data-menu-button>@if($user?->avatar_url)<img src="{{ $user->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-white">@elseif($user)<span class="grid h-8 w-8 place-items-center rounded-full bg-brand-900 text-xs font-black text-marigold-300">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</span>@else<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21a8 8 0 0 0-16 0m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>@endif</button>
                        <div class="site-header-menu absolute right-0 top-full mt-3 w-64 rounded-[1.4rem] border border-stone-200/70 bg-white p-2 shadow-[0_30px_60px_-24px_rgb(14_33_25/.4)]" data-menu>
                            @auth
                                <div class="mb-1 rounded-2xl bg-parchment px-4 py-3"><p class="text-[11px] text-stone-500">Signed in as</p><p class="truncate text-sm font-black text-ink">{{ $user->name }}</p><p class="mt-1 text-[11px] font-bold text-brand-700">{{ number_format($user->loyalty_points) }} loyalty points</p></div>
                                <a href="{{ route('profile') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-brand-50">My account</a>
                                <a href="{{ route('orders') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-brand-50">Orders & tracking</a>
                                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-brand-50"><span>Notifications</span>@if($unreadNotificationCount)<span class="rounded-full bg-saffron px-2 py-0.5 text-[10px] font-black text-white">{{ $unreadNotificationCount }}</span>@endif</a>
                                <a href="{{ route('wishlist.index') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-brand-50">Wishlist</a>
                                @if($user->isStaff())<a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-brand-50">Admin panel</a>@endif
                                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-stone-100 pt-1">@csrf<button type="submit" class="w-full rounded-xl px-4 py-2.5 text-left text-sm font-semibold text-red-700 hover:bg-red-50">Sign out</button></form>
                            @else
                                <div class="p-3"><p class="font-display text-xl text-brand-950">Welcome</p><p class="mt-1 text-xs leading-5 text-stone-500">Track orders, save favourites and earn rewards.</p></div>
                                <a href="{{ route('login') }}" class="button-primary w-full">Sign in</a>
                                <a href="{{ route('register') }}" class="mt-1 block rounded-xl px-4 py-2.5 text-center text-sm font-bold text-brand-700 hover:bg-brand-50">Create an account</a>
                            @endauth
                        </div>
                    </div>
                    <a href="{{ route('cart.index') }}" class="icon-button" aria-label="Shopping cart{{ $cartCount ? ', '.$cartCount.' items' : '' }}" aria-controls="cartDrawer" data-drawer-open="cartDrawer" data-cart-trigger><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14l-1 13H6L5 8Zm3 2V6a4 4 0 0 1 8 0v4"/></svg><span class="count-badge {{ $cartCount ? '' : 'hidden' }}" data-cart-count>{{ $cartCount }}</span></a>
                </div>
            </div>
        </div>
    </header>

    {{-- Search overlay --}}
    <div id="searchPanel" class="search-overlay" role="dialog" aria-modal="true" aria-label="Search products" data-search-panel>
        <div class="site-shell py-6 sm:py-10">
            <div class="flex items-center justify-between"><p class="eyebrow eyebrow-line">Search the pantry</p><button type="button" class="icon-button" aria-label="Close search" data-search-close><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18"/></svg></button></div>
            <form method="GET" action="{{ route('products.index') }}" class="relative mt-6" role="search" data-search-form>
                <label for="globalSearch" class="sr-only">Search products</label>
                <svg class="pointer-events-none absolute left-0 top-1/2 h-7 w-7 -translate-y-1/2 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                <input id="globalSearch" name="search" type="search" autocomplete="off" placeholder="Honey, rajma, Kinnaur…" class="search-input" aria-controls="searchResults" aria-autocomplete="list">
            </form>
            <div class="mt-8 grid gap-10 lg:grid-cols-[1fr_1.6fr]">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[.18em] text-stone-500">Popular collections</p>
                    <div class="mt-4 flex flex-wrap gap-2">@foreach($navigationCategories->take(8) as $navigationCategory)<a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="chip">{{ $navigationCategory->name }}</a>@endforeach</div>
                </div>
                <div id="searchResults" aria-live="polite" data-search-results>
                    <p class="text-[11px] font-black uppercase tracking-[.18em] text-stone-500">Start typing to see instant results</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile navigation drawer --}}
    <aside id="mobileMenu" class="drawer drawer-left" aria-label="Mobile navigation" data-drawer>
        <div class="flex items-center justify-between border-b border-stone-200/80 px-6 py-4"><p class="font-display text-2xl font-medium text-brand-950">Menu</p><button type="button" class="icon-button" aria-label="Close navigation" data-drawer-close><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18"/></svg></button></div>
        <nav class="flex-1 overflow-y-auto px-4 py-5" aria-label="Mobile navigation">
            <a href="{{ route('products.index') }}" class="stagger-item flex items-center justify-between rounded-2xl px-3 py-3.5 font-display text-2xl text-brand-950 hover:bg-white" style="--i:0">Shop all <span aria-hidden="true">→</span></a>
            <details class="stagger-item group" style="--i:1"><summary class="flex list-none items-center justify-between rounded-2xl px-3 py-3.5 font-display text-2xl text-brand-950 hover:bg-white">Collections <svg class="h-5 w-5 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></summary><div class="grid gap-1 px-2 pb-3">@foreach($navigationCategories as $navigationCategory)<a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="flex justify-between rounded-xl px-3 py-2 text-sm text-stone-600 hover:bg-white"><span>{{ $navigationCategory->name }}</span><small class="text-stone-400">{{ $navigationCategory->products_count }}</small></a>@endforeach</div></details>
            @foreach($navigation as $item)
                <a href="{{ route($item['route']) }}" class="stagger-item flex items-center justify-between rounded-2xl px-3 py-3.5 font-display text-2xl text-brand-950 hover:bg-white" style="--i:{{ $loop->iteration + 1 }}" @if(request()->routeIs($item['active'])) aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
            <div class="stagger-item mt-4 grid gap-1 border-t border-stone-200/80 pt-4" style="--i:8">
                @auth
                    <a href="{{ route('profile') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold hover:bg-white">My account</a>
                    <a href="{{ route('orders') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold hover:bg-white">My orders</a>
                    <a href="{{ route('wishlist.index') }}" class="rounded-xl px-3 py-2.5 text-sm font-bold hover:bg-white">Wishlist</a>
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-bold hover:bg-white"><span>Notifications</span>@if($unreadNotificationCount)<span class="rounded-full bg-saffron px-2 py-0.5 text-xs text-white">{{ $unreadNotificationCount }}</span>@endif</a>
                @else
                    <a href="{{ route('login') }}" class="button-primary w-full">Sign in</a>
                    <a href="{{ route('register') }}" class="button-secondary mt-2 w-full">Create an account</a>
                @endauth
            </div>
        </nav>
        <p class="border-t border-stone-200/80 px-6 py-4 text-xs text-stone-500">Free delivery on orders over {{ $freeDeliveryThreshold }} · Packed in Dehradun</p>
    </aside>

    {{-- Cart drawer --}}
    <aside id="cartDrawer" class="drawer" aria-label="Your cart" data-drawer data-cart-drawer>
        <div class="flex items-center justify-between border-b border-stone-200/80 px-6 py-4"><p class="font-display text-2xl font-medium text-brand-950">Your basket <span class="ml-1 align-middle text-sm font-sans font-bold text-stone-400" data-cart-drawer-count>({{ $cartCount }})</span></p><button type="button" class="icon-button" aria-label="Close cart" data-drawer-close><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M6 6l12 12M18 6 6 18"/></svg></button></div>
        <div class="flex min-h-0 flex-1 flex-col" data-cart-drawer-body>@include('partials.cart-drawer-body', ['miniCart' => $miniCart])</div>
    </aside>
    <div class="overlay-backdrop" data-overlay-backdrop></div>

    {{-- Notices --}}
    <div class="toast-region" data-toast-region aria-live="polite">
        @if(session('success'))<div role="status" class="toast" data-dismissible data-autodismiss><span class="toast-icon" aria-hidden="true"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 12 4 4L19 6"/></svg></span><p class="flex-1 pt-1 font-semibold">{{ session('success') }}</p><button type="button" class="-mr-1 grid h-7 w-7 place-items-center rounded-full text-white/70 hover:bg-white/10 hover:text-white" aria-label="Dismiss message" data-dismiss>×</button><span class="toast-progress" aria-hidden="true"></span></div>@endif
        @if(session('error'))<div role="alert" class="toast is-error" data-dismissible><span class="toast-icon" aria-hidden="true">!</span><p class="flex-1 pt-1 font-semibold">{{ session('error') }}</p><button type="button" class="-mr-1 grid h-7 w-7 place-items-center rounded-full text-white/70 hover:bg-white/10 hover:text-white" aria-label="Dismiss message" data-dismiss>×</button></div>@endif
    </div>
    @if($errors->any())
        <div class="site-shell pt-6"><div role="alert" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800" data-dismissible><div class="flex items-start justify-between gap-4"><strong>Please check the highlighted information.</strong><button type="button" class="text-lg leading-none" aria-label="Dismiss message" data-dismiss>×</button></div><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
    @endif

    <main id="main-content" class="{{ $fullBleed ? '' : 'site-shell py-8 sm:py-12' }}">@yield('content')</main>

    {{-- Footer --}}
    <footer class="relative mt-20 bg-brand-950 text-stone-200">
        <svg class="ridge-divider absolute bottom-full left-0 text-brand-950" viewBox="0 0 1440 90" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 90V62l90-30 70 22 110-46 95 38 80-20 120 44 90-34 110 30 95-48 105 40 85-22 110 34 90-26 80 18 110-24v54Z"/></svg>
        <div class="site-shell pb-10 pt-12">
            <div class="grid gap-8 rounded-[2rem] bg-white/[.04] p-7 ring-1 ring-white/10 sm:p-10 lg:grid-cols-[1.3fr_1fr] lg:items-center" data-reveal>
                <div><p class="eyebrow !text-marigold-300">Pahadi Roots rewards</p><h2 class="font-display mt-3 text-3xl font-medium leading-tight text-white sm:text-4xl">Join the circle. <em class="text-marigold-300">Earn as you shop,</em> share with friends.</h2></div>
                <div class="flex flex-wrap gap-3 lg:justify-end"><a href="{{ auth()->check() ? route('profile') : route('register') }}" class="button-accent">{{ auth()->check() ? 'View my rewards' : 'Create a free account' }} <span class="button-arrow" aria-hidden="true">→</span></a><a href="{{ route('rewards') }}" class="button-ghost-light">How rewards work</a></div>
            </div>

            <div class="mt-14 grid gap-10 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
                <div class="lg:pr-8"><p class="font-display text-3xl font-medium text-white">Pahadi Roots</p><p class="mt-4 text-sm leading-7 text-stone-300">Traceable mountain produce and thoughtful gifts, sourced with care from Uttarakhand and neighbouring Himalayan communities.</p><p class="mt-4 text-sm text-stone-400">Rajpur Road, Dehradun<br>Uttarakhand 248001</p><div class="mt-6"><p class="text-[10px] font-black uppercase tracking-[.18em] text-brand-300">Follow our journey</p><div class="mt-3 flex items-center gap-2" aria-label="Pahadi Roots social media">@foreach([
                    ['instagram', 'Instagram', 'M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2Zm0 2A3.8 3.8 0 0 0 4 7.8v8.4A3.8 3.8 0 0 0 7.8 20h8.4a3.8 3.8 0 0 0 3.8-3.8V7.8A3.8 3.8 0 0 0 16.2 4H7.8ZM17.3 5.5a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z'],
                    ['facebook', 'Facebook', 'M13.5 22v-9h3l.5-3.5h-3.5V7.3c0-1 .3-1.8 1.8-1.8H17V2.4c-.5-.1-1.7-.2-3-.2-3 0-5 1.8-5 5V9.5H6V13h3v9h4.5Z'],
                    ['youtube', 'YouTube', 'M21.6 7.2a3 3 0 0 0-2.1-2.1C17.6 4.6 12 4.6 12 4.6s-5.6 0-7.5.5a3 3 0 0 0-2.1 2.1C2 9.1 2 12 2 12s0 2.9.4 4.8a3 3 0 0 0 2.1 2.1c1.9.5 7.5.5 7.5.5s5.6 0 7.5-.5a3 3 0 0 0 2.1-2.1c.4-1.9.4-4.8.4-4.8s0-2.9-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z'],
                    ['pinterest', 'Pinterest', 'M12 2a10 10 0 0 0-3.7 19.3c-.1-1.6 0-3.4.4-5l1.3-5.4s-.3-.7-.3-1.8c0-1.7 1-3 2.2-3 1 0 1.5.8 1.5 1.8 0 1.1-.7 2.8-1.1 4.3-.3 1.3.7 2.4 2 2.4 2.4 0 4.2-2.5 4.2-6.1 0-3.2-2.3-5.4-5.6-5.4-3.8 0-6 2.8-6 5.8 0 1.1.4 2.4 1 3 .1.1.1.2.1.4l-.4 1.6c-.1.5-.5.6-.9.4-1.8-.8-2.9-3.3-2.9-5.3 0-4.3 3.1-8.2 9-8.2 4.7 0 8.4 3.4 8.4 7.9 0 4.7-3 8.5-7.1 8.5-1.4 0-2.7-.7-3.1-1.6l-.9 3.3c-.3 1.2-1.2 2.8-1.8 3.7.9.3 1.8.4 2.7.4A10 10 0 0 0 12 2Z'],
                ] as [$network, $label, $path])@if(config('services.social.'.$network))<a href="{{ config('services.social.'.$network) }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-brand-200 transition duration-300 hover:-translate-y-1 hover:border-marigold-300 hover:bg-white/10 hover:text-marigold-300" aria-label="Follow Pahadi Roots on {{ $label }}"><svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $path }}"/></svg></a>@endif @endforeach<button type="button" data-share data-share-title="Pahadi Roots" data-share-text="Traceable mountain produce and thoughtful gifts from Uttarakhand." data-share-url="{{ route('home') }}" class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-brand-200 transition duration-300 hover:-translate-y-1 hover:border-marigold-300 hover:bg-white/10 hover:text-marigold-300" aria-label="Share Pahadi Roots"><svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.8"/><circle cx="6" cy="12" r="2.5" stroke-width="1.8"/><circle cx="18" cy="19" r="2.5" stroke-width="1.8"/><path stroke-width="1.8" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button></div></div></div>
                <div><h2 class="text-xs font-black uppercase tracking-[.18em] text-marigold-300">Shop</h2><ul class="mt-5 space-y-3 text-sm"><li><a href="{{ route('products.index') }}" class="link-underline hover:text-white">All products</a></li>@foreach($navigationCategories->take(5) as $navigationCategory)<li><a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="link-underline hover:text-white">{{ $navigationCategory->name }}</a></li>@endforeach<li><a href="{{ route('bundles.index') }}" class="link-underline hover:text-white">Gift boxes</a></li></ul></div>
                <div><h2 class="text-xs font-black uppercase tracking-[.18em] text-marigold-300">Information</h2><ul class="mt-5 space-y-3 text-sm"><li><a href="{{ route('about') }}" class="link-underline hover:text-white">Our story</a></li><li><a href="{{ route('blog') }}" class="link-underline hover:text-white">Journal</a></li><li><a href="{{ route('rewards') }}" class="link-underline hover:text-white">Rewards & referrals</a></li><li><a href="{{ route('faq') }}" class="link-underline hover:text-white">FAQs</a></li><li><a href="{{ route('shipping.policy') }}" class="link-underline hover:text-white">Shipping</a></li><li><a href="{{ route('returns.policy') }}" class="link-underline hover:text-white">Returns</a></li><li><a href="{{ route('privacy.policy') }}" class="link-underline hover:text-white">Privacy</a></li><li><a href="{{ route('terms') }}" class="link-underline hover:text-white">Terms</a></li><li><a href="{{ route('editorial.policy') }}" class="link-underline hover:text-white">Editorial policy</a></li></ul></div>
                <div><h2 class="text-xs font-black uppercase tracking-[.18em] text-marigold-300">Customer care</h2><p class="mt-5 text-sm leading-7 text-stone-300">Monday–Saturday, 10:00 AM–6:00 PM</p><a href="mailto:support@pahadiroots.in" class="mt-3 block text-sm font-bold text-white hover:text-marigold-300">support@pahadiroots.in</a><a href="{{ route('contact') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-marigold-300 hover:text-marigold-200">Contact our team <span class="button-arrow" aria-hidden="true">→</span></a><p class="mt-6 flex items-center gap-2 text-xs text-stone-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.7" d="M7 10V7a5 5 0 0 1 10 0v3m2 0H5v11h14V10Z"/></svg>Secure checkout powered by Stripe</p></div>
            </div>

            <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-7 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-[10px] font-black uppercase tracking-[.18em] text-brand-300">Payments accepted</p><p class="mt-1 text-xs text-stone-400">Cards and UPI are processed securely at checkout.</p></div>
                <div class="flex flex-wrap items-center gap-2" role="list" aria-label="Accepted payment methods">
                    <span class="payment-mark" role="listitem" aria-label="Visa"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#fff"/><text x="28" y="19" text-anchor="middle" fill="#1434CB" font-family="Arial,sans-serif" font-size="14" font-weight="900" font-style="italic">VISA</text></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="Mastercard"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#fff"/><circle cx="23" cy="14" r="8" fill="#EB001B"/><circle cx="33" cy="14" r="8" fill="#F79E1B"/><path d="M28 7.9a8 8 0 0 1 0 12.2 8 8 0 0 1 0-12.2Z" fill="#FF5F00"/></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="RuPay"><svg viewBox="0 0 64 28" aria-hidden="true"><rect width="64" height="28" rx="4" fill="#fff"/><text x="25" y="18.5" text-anchor="middle" fill="#17457A" font-family="Arial,sans-serif" font-size="12" font-weight="800">RuPay</text><path d="m45 7 6 7-6 7h4l6-7-6-7h-4Z" fill="#F47B20"/><path d="m40 7 6 7-6 7h4l6-7-6-7h-4Z" fill="#14A05A"/></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="American Express"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#2E77BC"/><text x="28" y="12" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="7" font-weight="900">AMERICAN</text><text x="28" y="21" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="8" font-weight="900">EXPRESS</text></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="UPI"><svg viewBox="0 0 60 28" aria-hidden="true"><rect width="60" height="28" rx="4" fill="#fff"/><text x="23" y="19" text-anchor="middle" fill="#222" font-family="Arial,sans-serif" font-size="14" font-weight="900" font-style="italic">UPI</text><path d="m39 6 6 8-6 8h4l6-8-6-8h-4Z" fill="#E87522"/><path d="m44 6 6 8-6 8h4l6-8-6-8h-4Z" fill="#169B62"/></svg></span>
                </div>
            </div>

            <p class="font-display pointer-events-none mt-10 select-none bg-gradient-to-b from-white/[.13] to-white/0 bg-clip-text text-center text-[clamp(3.5rem,15vw,13rem)] font-medium leading-[.8] tracking-[-.05em] text-transparent" aria-hidden="true">Pahadi Roots</p>
            <div class="mt-6 grid gap-3 border-t border-white/10 pt-6 text-xs text-stone-400 sm:grid-cols-3 sm:items-center"><p>© {{ date('Y') }} Pahadi Roots. All rights reserved.</p><p class="sm:text-center">Made for the hills, packed in Dehradun.</p><p class="sm:text-right">Made with love ❤️ by <span class="font-bold text-stone-200">Rajendra Panwar</span></p></div>
        </div>
    </footer>

    <button type="button" class="back-to-top" aria-label="Back to top" data-back-to-top><svg class="ring h-12 w-12" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="none" stroke="#f2c063" stroke-width="2.5" stroke-linecap="round"/></svg><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 15 6-6 6 6"/></svg></button>
    <a href="https://wa.me/{{ config('commerce.whatsapp_number', '919876543210') }}?text={{ urlencode('Hello Pahadi Roots, I need help with an order.') }}" target="_blank" rel="noopener noreferrer" class="whatsapp-fab group fixed bottom-5 right-5 z-40 grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-[0_14px_30px_-10px_rgb(37_211_102/.7)] transition duration-300 hover:-translate-y-1 hover:scale-105" aria-label="Chat with Pahadi Roots on WhatsApp"><span class="absolute inset-0 animate-ping rounded-full bg-[#25D366] opacity-20 [animation-duration:2.5s]" aria-hidden="true"></span><svg class="relative h-7 w-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2A9.84 9.84 0 0 0 3.6 16.9L2 22l5.25-1.53A9.96 9.96 0 1 0 12.04 2Zm5.82 14.08c-.24.68-1.4 1.3-1.93 1.37-.5.06-1.12.08-1.81-.14-.42-.13-.96-.31-1.65-.61-2.9-1.26-4.8-4.2-4.94-4.39-.14-.19-1.18-1.57-1.18-3 0-1.44.74-2.14 1.01-2.43.27-.3.6-.37.8-.37h.57c.18 0 .43-.07.67.51.24.58.82 2 .9 2.14.07.14.12.31.02.5-.1.2-.14.32-.29.49-.14.17-.3.38-.43.51-.14.15-.29.3-.12.59.17.29.75 1.24 1.61 2 .1.09 1.75 1.54 3.22 2.14.29.12.52.1.71-.12.2-.21.84-.98 1.06-1.32.22-.34.44-.29.74-.17.31.12 1.94.92 2.27 1.09.34.17.56.25.65.39.08.15.08.83-.16 1.51Z"/></svg></a>

    @stack('scripts')
</body>
</html>
