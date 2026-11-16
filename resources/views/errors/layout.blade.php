{{-- Standalone error layout: no database-backed view composers, so it renders even when the app is unhealthy. --}}
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0e2119">
    <x-favicons />
    <title>@yield('title') | Pahadi Roots</title>
    @vite(['resources/css/app.css'])
</head>
<body class="storefront min-h-screen bg-snow text-ink antialiased">
    <main class="relative flex min-h-screen flex-col overflow-hidden">
        <header class="site-shell flex items-center justify-between py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="Pahadi Roots home"><x-logo class="h-11" alt="" /></a>
            <a href="{{ url('/products') }}" class="button-secondary hidden sm:inline-flex">Visit the shop</a>
        </header>
        <section class="site-shell relative z-10 flex flex-1 flex-col items-center justify-center py-12 text-center">
            <p class="font-display select-none bg-gradient-to-b from-moss to-sun bg-clip-text text-[clamp(7rem,24vw,15rem)] font-medium leading-[.85] tracking-[-.06em] text-transparent animate-[rise-in_1s_var(--ease-out-expo)_both]" aria-hidden="true">@yield('code')</p>
            <p class="eyebrow eyebrow-line mt-6 animate-[rise-in_1s_var(--ease-out-expo)_.1s_both]">@yield('eyebrow')</p>
            <h1 class="font-display mt-4 max-w-2xl text-[clamp(2rem,4.5vw,3.25rem)] font-medium leading-[1.05] text-ink animate-[rise-in_1s_var(--ease-out-expo)_.18s_both]">@yield('heading')</h1>
            <p class="lead mt-5 max-w-xl animate-[rise-in_1s_var(--ease-out-expo)_.26s_both]">@yield('message')</p>
            <div class="mt-9 flex flex-wrap justify-center gap-3 animate-[rise-in_1s_var(--ease-out-expo)_.34s_both]">@yield('actions')</div>
        </section>
    </main>
</body>
</html>
