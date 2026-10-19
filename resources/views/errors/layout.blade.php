{{-- Standalone error layout: no database-backed view composers, so it renders even when the app is unhealthy. --}}
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0e2119">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title') | Pahadi Roots</title>
    @vite(['resources/css/app.css'])
</head>
<body class="storefront min-h-screen bg-cream text-ink antialiased">
    <main class="relative flex min-h-screen flex-col overflow-hidden">
        <header class="site-shell flex items-center justify-between py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="Pahadi Roots home"><svg class="h-11 w-11" viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect width="48" height="48" rx="14" fill="#0e2119"/><path d="m8 31 9-13 6 8 6-12 11 17H8Z" fill="#F8F4EB"/><path d="m13 31 7-7 4 5 6-8 7 10H13Z" fill="#D78A18"/><path d="M10 35c8-3 20-3 28 0" stroke="#F8F4EB" stroke-width="2" stroke-linecap="round"/></svg><span class="font-display text-2xl font-semibold text-brand-950">Pahadi Roots</span></a>
            <a href="{{ url('/products') }}" class="button-secondary hidden sm:inline-flex">Visit the shop</a>
        </header>
        <section class="site-shell relative z-10 flex flex-1 flex-col items-center justify-center py-12 text-center">
            <p class="font-display select-none bg-gradient-to-b from-brand-900 to-brand-500 bg-clip-text text-[clamp(7rem,24vw,15rem)] font-medium leading-[.85] tracking-[-.06em] text-transparent animate-[rise-in_1s_var(--ease-out-expo)_both]" aria-hidden="true">@yield('code')</p>
            <p class="eyebrow eyebrow-line mt-6 animate-[rise-in_1s_var(--ease-out-expo)_.1s_both]">@yield('eyebrow')</p>
            <h1 class="font-display mt-4 max-w-2xl text-[clamp(2rem,4.5vw,3.25rem)] font-medium leading-[1.05] text-brand-950 animate-[rise-in_1s_var(--ease-out-expo)_.18s_both]">@yield('heading')</h1>
            <p class="lead mt-5 max-w-xl animate-[rise-in_1s_var(--ease-out-expo)_.26s_both]">@yield('message')</p>
            <div class="mt-9 flex flex-wrap justify-center gap-3 animate-[rise-in_1s_var(--ease-out-expo)_.34s_both]">@yield('actions')</div>
        </section>
        <svg class="pointer-events-none absolute inset-x-0 bottom-0 h-[38vh] w-full text-brand-100/70" viewBox="0 0 1440 320" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 320V210l120-70 90 40 170-130 140 90 110-50 200 140 150-90 120 50 180-120 160 110v140Z"/></svg>
        <svg class="pointer-events-none absolute inset-x-0 bottom-0 h-[24vh] w-full text-brand-200/60" viewBox="0 0 1440 220" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 220V150l160-60 120 50 200-110 160 90 140-40 180 90 170-70 150 40 160-60v130Z"/></svg>
    </main>
</body>
</html>
