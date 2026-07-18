@php
    $seoTitle = trim($__env->yieldContent('title', config('seo.default_title')));
    $seoDescription = trim($__env->yieldContent('meta_description', config('seo.default_description')));
    $privateRoute = request()->routeIs('admin.*', 'cart.*', 'checkout.*', 'login', 'register', 'password.*', 'verification.*', 'profile', 'orders*', 'wishlist.*');
    $filteredListing = request()->routeIs('products.index') && request()->hasAny(['search', 'sort', 'min_price', 'max_price', 'availability', 'sale', 'featured', 'origin', 'rating', 'weight']);
    $robots = trim($__env->yieldContent('robots', $privateRoute ? 'noindex, nofollow' : ($filteredListing ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')));
    $canonicalUrl = trim($__env->yieldContent('canonical', request()->url()));
    $socialImage = trim($__env->yieldContent('og_image', asset('assets/img/hero/himalayan-collection-v2.webp')));
    $socialImageAlt = trim($__env->yieldContent('og_image_alt', 'Pahadi Roots Himalayan pantry collection'));
@endphp
<!DOCTYPE html>
<html lang="{{ config('seo.language', 'en-IN') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="theme-color" content="#1d362b">
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
    <meta property="og:image:alt" content="{{ $socialImageAlt }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <meta name="twitter:image:alt" content="{{ $socialImageAlt }}">
    <title>{{ $seoTitle }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen antialiased">
    <a href="#main-content" class="sr-only z-[100] rounded-lg bg-white px-4 py-3 font-bold text-brand-900 focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to content</a>

    <header class="sticky top-0 z-50 border-b border-stone-200/90 bg-white/95 backdrop-blur-xl">
        <div class="bg-brand-950 text-white">
            <div class="site-shell flex min-h-9 items-center justify-between gap-4 py-1.5 text-[11px] font-semibold sm:text-xs">
                <p class="flex items-center gap-2"><svg class="h-3.5 w-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg><span>Rooted in Uttarakhand</span></p>
                <div class="flex items-center gap-4"><a href="{{ route('faq') }}" class="text-stone-200 hover:text-white">Help</a><a href="{{ auth()->check() ? route('orders') : route('login') }}" class="text-stone-200 hover:text-white">Track order</a></div>
            </div>
        </div>

        <div class="site-shell flex h-[72px] items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="Pahadi Roots home">
                <svg class="h-10 w-10 sm:h-11 sm:w-11" viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect width="48" height="48" rx="14" fill="#1d362b"/><path d="m8 31 9-13 6 8 6-12 11 17H8Z" fill="#F8F4EB"/><path d="m13 31 7-7 4 5 6-8 7 10H13Z" fill="#D78A18"/><path d="M10 35c8-3 20-3 28 0" stroke="#F8F4EB" stroke-width="2" stroke-linecap="round"/></svg>
                <span class="leading-none"><strong class="font-display block text-lg font-semibold tracking-tight text-brand-950 sm:text-2xl">Pahadi Roots</strong><small class="mt-1 hidden text-[9px] font-extrabold uppercase tracking-[.2em] text-brand-600 sm:block">From the hills</small></span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary navigation">
                <a href="{{ route('home') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-bold {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-800' : 'text-stone-700 hover:bg-stone-50 hover:text-brand-700' }}">Home</a>
                <div class="site-header-group relative" data-menu-group>
                    <div class="flex items-center">
                        <a href="{{ route('products.index') }}" class="rounded-l-lg py-2.5 pl-3.5 pr-2 text-sm font-bold {{ request()->routeIs('products.*') ? 'bg-brand-50 text-brand-800' : 'text-stone-700 hover:bg-stone-50 hover:text-brand-700' }}">Shop</a>
                        <button type="button" class="rounded-r-lg px-2 py-2.5 text-stone-600 hover:bg-brand-50 hover:text-brand-800" aria-label="Open shop menu" aria-expanded="false" data-menu-button><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button>
                    </div>
                    <div class="site-header-menu absolute left-0 top-full mt-2 w-64 rounded-xl border border-stone-200 bg-white p-2 shadow-xl" data-menu>
                        <a href="{{ route('products.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-extrabold text-brand-900 hover:bg-brand-50">All products</a>
                        @foreach($navigationCategories as $navigationCategory)
                            <a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="block rounded-lg px-3 py-2.5 text-sm text-stone-650 hover:bg-brand-50 hover:text-brand-800">{{ $navigationCategory->name }}</a>
                        @endforeach
                        <a href="{{ route('bundles.index') }}" class="mt-1 block border-t border-stone-100 px-3 pt-3 pb-2 text-sm font-bold text-saffron hover:text-amber-700">Gift boxes & bundles</a>
                    </div>
                </div>
                <a href="{{ route('about') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-bold {{ request()->routeIs('about') ? 'bg-brand-50 text-brand-800' : 'text-stone-700 hover:bg-stone-50 hover:text-brand-700' }}">Our story</a>
                <a href="{{ route('blog') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-bold {{ request()->routeIs('blog*') ? 'bg-brand-50 text-brand-800' : 'text-stone-700 hover:bg-stone-50 hover:text-brand-700' }}">Journal</a>
                <a href="{{ route('rewards') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-bold {{ request()->routeIs('rewards') ? 'bg-brand-50 text-brand-800' : 'text-stone-700 hover:bg-stone-50 hover:text-brand-700' }}">Rewards</a>
                <a href="{{ route('contact') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-bold {{ request()->routeIs('contact') ? 'bg-brand-50 text-brand-800' : 'text-stone-700 hover:bg-stone-50 hover:text-brand-700' }}">Contact</a>
            </nav>

            <div class="flex items-center gap-0.5 sm:gap-1">
                <button id="searchButton" type="button" class="icon-button" aria-label="Open product search" aria-expanded="false" aria-controls="searchPanel"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg></button>
                <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="icon-button" aria-label="Wishlist"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></a>
                <div class="site-header-group relative hidden sm:block" data-menu-group>
                    <button type="button" class="icon-button" aria-label="Open account menu" aria-expanded="false" data-menu-button><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21a8 8 0 0 0-16 0m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg></button>
                    <div class="site-header-menu absolute right-0 top-full mt-2 w-56 rounded-xl border border-stone-200 bg-white p-2 shadow-xl" data-menu>
                        @auth
                            <p class="px-3 pb-2 pt-1 text-xs text-stone-500">Signed in as <strong class="block truncate text-stone-800">{{ auth()->user()->name }}</strong></p>
                            <a href="{{ route('profile') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-brand-50">Account</a>
                            <a href="{{ route('orders') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-brand-50">Orders</a>
                            <a href="{{ route('wishlist.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-brand-50">Wishlist</a>
                            @if(auth()->user()->isStaff())<a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-brand-50">Admin panel</a>@endif
                            <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-stone-100 pt-1">@csrf<button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-red-700 hover:bg-red-50">Sign out</button></form>
                        @else
                            <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-bold hover:bg-brand-50">Sign in</a>
                            <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 text-sm font-bold text-brand-700 hover:bg-brand-50">Create account</a>
                        @endauth
                    </div>
                </div>
                <a href="{{ route('cart.index') }}" class="icon-button relative" aria-label="Shopping cart{{ $cartCount ? ', '.$cartCount.' items' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14l-1 13H6L5 8Zm3 2V6a4 4 0 0 1 8 0v4"/></svg>@if($cartCount)<span class="absolute right-0 top-0 grid h-5 min-w-5 place-items-center rounded-full bg-saffron px-1 text-[10px] font-black text-white">{{ $cartCount }}</span>@endif</a>
                <button id="mobileMenuButton" type="button" class="icon-button lg:!hidden" aria-label="Open navigation" aria-expanded="false" aria-controls="mobileMenu"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16"/></svg></button>
            </div>
        </div>

        <div id="searchPanel" class="hidden border-t border-stone-200 bg-white py-4">
            <form method="GET" action="{{ route('products.index') }}" class="site-shell flex gap-2"><label for="globalSearch" class="sr-only">Search products</label><input id="globalSearch" name="search" type="search" autocomplete="off" placeholder="Search honey, tea, spices, grains…" class="min-w-0 flex-1 rounded-xl px-4"><button class="button-primary" type="submit">Search</button><button id="closeSearch" class="button-secondary hidden sm:inline-flex" type="button">Close</button></form>
        </div>

        <div id="mobileMenu" class="hidden border-t border-stone-200 bg-white lg:hidden">
            <nav class="site-shell grid gap-1 py-4" aria-label="Mobile navigation">
                <a href="{{ route('products.index') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">Shop all products</a>
                @foreach($navigationCategories as $navigationCategory)<a href="{{ route('products.index', ['category' => $navigationCategory->slug]) }}" class="rounded-lg px-3 py-2 text-sm text-stone-600 hover:bg-brand-50">{{ $navigationCategory->name }}</a>@endforeach
                <a href="{{ route('bundles.index') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">Gift boxes & bundles</a>
                <div class="my-1 border-t border-stone-100"></div>
                <a href="{{ route('about') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">Our story</a><a href="{{ route('blog') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">Journal</a><a href="{{ route('rewards') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">Rewards & referrals</a><a href="{{ route('contact') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">Contact</a>
                @auth<a href="{{ route('profile') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">My account</a><a href="{{ route('orders') }}" class="rounded-lg px-3 py-3 font-bold hover:bg-brand-50">My orders</a>@else<a href="{{ route('login') }}" class="rounded-lg px-3 py-3 font-bold text-brand-700 hover:bg-brand-50">Sign in</a>@endauth
            </nav>
        </div>
    </header>

    @if(session('success') || session('error') || $errors->any())
        <div class="site-shell pt-5">
            @if(session('success'))<div role="status" class="flex items-start justify-between gap-4 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-900"><span>{{ session('success') }}</span><button type="button" aria-label="Dismiss message" onclick="this.parentElement.remove()">×</button></div>@endif
            @if(session('error'))<div role="alert" class="flex items-start justify-between gap-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800"><span>{{ session('error') }}</span><button type="button" aria-label="Dismiss message" onclick="this.parentElement.remove()">×</button></div>@endif
            @if($errors->any())<div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><strong>Please check the highlighted information.</strong><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        </div>
    @endif

    <main id="main-content" class="site-shell py-8 sm:py-10">@yield('content')</main>

    <footer class="mt-16 bg-brand-950 text-stone-200">
        <div class="site-shell py-14">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:pr-8"><p class="font-display text-2xl font-semibold text-white">Pahadi Roots</p><p class="mt-4 text-sm leading-7 text-stone-300">Traceable mountain produce and thoughtful gifts, sourced with care from Uttarakhand and neighbouring Himalayan communities.</p><p class="mt-4 text-sm text-stone-300">Rajpur Road, Dehradun<br>Uttarakhand 248001</p><div class="mt-6"><p class="text-[10px] font-black uppercase tracking-[.16em] text-brand-300">Follow our journey</p><div class="mt-3 flex items-center gap-2" aria-label="Pahadi Roots social media">@foreach([
                    ['instagram', 'Instagram', 'M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2Zm0 2A3.8 3.8 0 0 0 4 7.8v8.4A3.8 3.8 0 0 0 7.8 20h8.4a3.8 3.8 0 0 0 3.8-3.8V7.8A3.8 3.8 0 0 0 16.2 4H7.8ZM17.3 5.5a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z'],
                    ['facebook', 'Facebook', 'M13.5 22v-9h3l.5-3.5h-3.5V7.3c0-1 .3-1.8 1.8-1.8H17V2.4c-.5-.1-1.7-.2-3-.2-3 0-5 1.8-5 5V9.5H6V13h3v9h4.5Z'],
                    ['youtube', 'YouTube', 'M21.6 7.2a3 3 0 0 0-2.1-2.1C17.6 4.6 12 4.6 12 4.6s-5.6 0-7.5.5a3 3 0 0 0-2.1 2.1C2 9.1 2 12 2 12s0 2.9.4 4.8a3 3 0 0 0 2.1 2.1c1.9.5 7.5.5 7.5.5s5.6 0 7.5-.5a3 3 0 0 0 2.1-2.1c.4-1.9.4-4.8.4-4.8s0-2.9-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z'],
                    ['pinterest', 'Pinterest', 'M12 2a10 10 0 0 0-3.7 19.3c-.1-1.6 0-3.4.4-5l1.3-5.4s-.3-.7-.3-1.8c0-1.7 1-3 2.2-3 1 0 1.5.8 1.5 1.8 0 1.1-.7 2.8-1.1 4.3-.3 1.3.7 2.4 2 2.4 2.4 0 4.2-2.5 4.2-6.1 0-3.2-2.3-5.4-5.6-5.4-3.8 0-6 2.8-6 5.8 0 1.1.4 2.4 1 3 .1.1.1.2.1.4l-.4 1.6c-.1.5-.5.6-.9.4-1.8-.8-2.9-3.3-2.9-5.3 0-4.3 3.1-8.2 9-8.2 4.7 0 8.4 3.4 8.4 7.9 0 4.7-3 8.5-7.1 8.5-1.4 0-2.7-.7-3.1-1.6l-.9 3.3c-.3 1.2-1.2 2.8-1.8 3.7.9.3 1.8.4 2.7.4A10 10 0 0 0 12 2Z'],
                ] as [$network, $label, $path])@if(config('services.social.'.$network))<a href="{{ config('services.social.'.$network) }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-brand-200 transition hover:border-amber-400 hover:bg-white/10 hover:text-amber-300" aria-label="Follow Pahadi Roots on {{ $label }}"><svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $path }}"/></svg></a>@endif @endforeach<button type="button" data-share data-share-title="Pahadi Roots" data-share-text="Traceable mountain produce and thoughtful gifts from Uttarakhand." data-share-url="{{ route('home') }}" class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-brand-200 transition hover:border-amber-400 hover:bg-white/10 hover:text-amber-300" aria-label="Share Pahadi Roots"><svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.8"/><circle cx="6" cy="12" r="2.5" stroke-width="1.8"/><circle cx="18" cy="19" r="2.5" stroke-width="1.8"/><path stroke-width="1.8" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button></div></div></div>
                <div><h2 class="text-xs font-black uppercase tracking-[.16em] text-amber-400">Shop</h2><ul class="mt-5 space-y-3 text-sm"><li><a href="{{ route('products.index') }}" class="hover:text-white">All products</a></li><li><a href="{{ route('bundles.index') }}" class="hover:text-white">Gift boxes</a></li><li><a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="hover:text-white">Wishlist</a></li><li><a href="{{ route('products.index', ['category' => 'honey']) }}" class="hover:text-white">Mountain honey</a></li></ul></div>
                <div><h2 class="text-xs font-black uppercase tracking-[.16em] text-amber-400">Information</h2><ul class="mt-5 space-y-3 text-sm"><li><a href="{{ route('about') }}" class="hover:text-white">Our story</a></li><li><a href="{{ route('rewards') }}" class="hover:text-white">Rewards & referrals</a></li><li><a href="{{ route('faq') }}" class="hover:text-white">FAQs</a></li><li><a href="{{ route('shipping.policy') }}" class="hover:text-white">Shipping</a></li><li><a href="{{ route('returns.policy') }}" class="hover:text-white">Returns</a></li><li><a href="{{ route('privacy.policy') }}" class="hover:text-white">Privacy</a></li><li><a href="{{ route('terms') }}" class="hover:text-white">Terms</a></li><li><a href="{{ route('editorial.policy') }}" class="hover:text-white">Editorial policy</a></li></ul></div>
                <div><h2 class="text-xs font-black uppercase tracking-[.16em] text-amber-400">Customer care</h2><p class="mt-5 text-sm leading-7 text-stone-300">Monday–Saturday, 10:00 AM–6:00 PM</p><a href="mailto:support@pahadiroots.in" class="mt-3 block text-sm font-bold text-white hover:text-amber-300">support@pahadiroots.in</a><a href="{{ route('contact') }}" class="mt-5 inline-flex text-sm font-bold text-amber-400 hover:text-amber-300">Contact our team →</a><p class="mt-6 text-xs text-stone-400">Secure checkout powered by Stripe</p></div>
            </div>
            <div class="mt-10 flex flex-col gap-4 border-t border-white/10 pt-7 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-[10px] font-black uppercase tracking-[.16em] text-brand-300">Payments accepted</p><p class="mt-1 text-xs text-stone-400">Cards and UPI are processed securely at checkout.</p></div>
                <div class="flex flex-wrap items-center gap-2" role="list" aria-label="Accepted payment methods">
                    <span class="payment-mark" role="listitem" aria-label="Visa"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#fff"/><text x="28" y="19" text-anchor="middle" fill="#1434CB" font-family="Arial,sans-serif" font-size="14" font-weight="900" font-style="italic">VISA</text></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="Mastercard"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#fff"/><circle cx="23" cy="14" r="8" fill="#EB001B"/><circle cx="33" cy="14" r="8" fill="#F79E1B"/><path d="M28 7.9a8 8 0 0 1 0 12.2 8 8 0 0 1 0-12.2Z" fill="#FF5F00"/></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="RuPay"><svg viewBox="0 0 64 28" aria-hidden="true"><rect width="64" height="28" rx="4" fill="#fff"/><text x="25" y="18.5" text-anchor="middle" fill="#17457A" font-family="Arial,sans-serif" font-size="12" font-weight="800">RuPay</text><path d="m45 7 6 7-6 7h4l6-7-6-7h-4Z" fill="#F47B20"/><path d="m40 7 6 7-6 7h4l6-7-6-7h-4Z" fill="#14A05A"/></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="American Express"><svg viewBox="0 0 56 28" aria-hidden="true"><rect width="56" height="28" rx="4" fill="#2E77BC"/><text x="28" y="12" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="7" font-weight="900">AMERICAN</text><text x="28" y="21" text-anchor="middle" fill="#fff" font-family="Arial,sans-serif" font-size="8" font-weight="900">EXPRESS</text></svg></span>
                    <span class="payment-mark" role="listitem" aria-label="UPI"><svg viewBox="0 0 60 28" aria-hidden="true"><rect width="60" height="28" rx="4" fill="#fff"/><text x="23" y="19" text-anchor="middle" fill="#222" font-family="Arial,sans-serif" font-size="14" font-weight="900" font-style="italic">UPI</text><path d="m39 6 6 8-6 8h4l6-8-6-8h-4Z" fill="#E87522"/><path d="m44 6 6 8-6 8h4l6-8-6-8h-4Z" fill="#169B62"/></svg></span>
                </div>
            </div>
            <div class="mt-7 grid gap-3 border-t border-white/10 pt-6 text-xs text-stone-400 sm:grid-cols-3 sm:items-center"><p>© {{ date('Y') }} Pahadi Roots. All rights reserved.</p><p class="sm:text-center">Made for the hills, packed in Dehradun.</p><p class="sm:text-right">Made with love ❤️ by <span class="font-bold text-stone-200">Rajendra Panwar</span></p></div>
        </div>
    </footer>

    <a href="https://wa.me/{{ config('commerce.whatsapp_number', '919876543210') }}?text={{ urlencode('Hello Pahadi Roots, I need help with an order.') }}" target="_blank" rel="noopener noreferrer" class="fixed bottom-5 right-5 z-40 grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl transition hover:-translate-y-1 hover:bg-[#1fbd5a]" aria-label="Chat with Pahadi Roots on WhatsApp"><svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2A9.84 9.84 0 0 0 3.6 16.9L2 22l5.25-1.53A9.96 9.96 0 1 0 12.04 2Zm5.82 14.08c-.24.68-1.4 1.3-1.93 1.37-.5.06-1.12.08-1.81-.14-.42-.13-.96-.31-1.65-.61-2.9-1.26-4.8-4.2-4.94-4.39-.14-.19-1.18-1.57-1.18-3 0-1.44.74-2.14 1.01-2.43.27-.3.6-.37.8-.37h.57c.18 0 .43-.07.67.51.24.58.82 2 .9 2.14.07.14.12.31.02.5-.1.2-.14.32-.29.49-.14.17-.3.38-.43.51-.14.15-.29.3-.12.59.17.29.75 1.24 1.61 2 .1.09 1.75 1.54 3.22 2.14.29.12.52.1.71-.12.2-.21.84-.98 1.06-1.32.22-.34.44-.29.74-.17.31.12 1.94.92 2.27 1.09.34.17.56.25.65.39.08.15.08.83-.16 1.51Z"/></svg></a>

    <script>
    (() => {
        const togglePanel = (button, panel, show) => { if (!button || !panel) return; panel.classList.toggle('hidden', !show); button.setAttribute('aria-expanded', String(show)); };
        const mobileButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileButton?.addEventListener('click', () => togglePanel(mobileButton, mobileMenu, mobileMenu.classList.contains('hidden')));
        const searchButton = document.getElementById('searchButton');
        const searchPanel = document.getElementById('searchPanel');
        searchButton?.addEventListener('click', () => { const show = searchPanel.classList.contains('hidden'); togglePanel(searchButton, searchPanel, show); if (show) document.getElementById('globalSearch')?.focus(); });
        document.getElementById('closeSearch')?.addEventListener('click', () => togglePanel(searchButton, searchPanel, false));
        document.querySelectorAll('[data-menu-group]').forEach(group => {
            const button = group.querySelector('[data-menu-button]');
            button?.addEventListener('click', event => { event.stopPropagation(); const open = group.dataset.open !== 'true'; document.querySelectorAll('[data-menu-group]').forEach(item => { item.dataset.open = 'false'; item.querySelector('[data-menu-button]')?.setAttribute('aria-expanded', 'false'); }); group.dataset.open = String(open); button.setAttribute('aria-expanded', String(open)); });
        });
        document.addEventListener('click', () => document.querySelectorAll('[data-menu-group]').forEach(group => { group.dataset.open = 'false'; group.querySelector('[data-menu-button]')?.setAttribute('aria-expanded', 'false'); }));
        document.addEventListener('keydown', event => { if (event.key !== 'Escape') return; togglePanel(mobileButton, mobileMenu, false); togglePanel(searchButton, searchPanel, false); document.querySelectorAll('[data-menu-group]').forEach(group => { group.dataset.open = 'false'; group.querySelector('[data-menu-button]')?.setAttribute('aria-expanded', 'false'); }); });
        const fallback = '{{ asset('assets/img/product-placeholder.svg') }}';
        document.querySelectorAll('img[data-image-fallback]').forEach(img => { const replace = () => { img.removeAttribute('data-image-fallback'); img.src = fallback; }; img.addEventListener('error', replace, {once:true}); if (img.complete && img.naturalWidth === 0) replace(); });
    })();
    </script>
    @stack('scripts')
</body>
</html>
