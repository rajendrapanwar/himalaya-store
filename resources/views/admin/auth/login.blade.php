<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0e2119">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Administrator sign in | Pahadi Roots</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#eef2ee] text-stone-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1.05fr_.95fr]">
        <section class="relative hidden overflow-hidden bg-brand-950 p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-16">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 18% 22%, #d78a18 0 1px, transparent 2px), radial-gradient(circle at 75% 70%, #91bda3 0 1px, transparent 2px); background-size: 42px 42px, 54px 54px"></div>
            <a href="{{ route('home') }}" class="relative flex items-center gap-3"><svg class="h-12 w-12" viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="14" fill="#f8f4eb"/><path d="m8 31 9-13 6 8 6-12 11 17H8Z" fill="#28503c"/><path d="m13 31 7-7 4 5 6-8 7 10H13Z" fill="#d78a18"/></svg><span><strong class="font-display block text-2xl font-semibold">Pahadi Roots</strong><small class="text-xs font-bold uppercase tracking-[.2em] text-brand-300">Secure administration</small></span></a>
            <div class="relative max-w-xl"><p class="text-xs font-black uppercase tracking-[.2em] text-amber-400">Operations workspace</p><h1 class="font-display mt-5 text-5xl font-semibold leading-[1.08]">Manage the mountain pantry with confidence.</h1><p class="mt-6 max-w-lg text-base leading-8 text-brand-100">A protected workspace for orders, catalogue, customers, fulfilment and business reporting.</p></div>
            <div class="relative flex items-center gap-3 text-xs text-brand-300"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.7" d="M7 10V7a5 5 0 0 1 10 0v3m2 0H5v11h14V10Z"/></svg><span>Password authentication followed by email verification code</span></div>
        </section>

        <section class="flex items-center justify-center p-5 sm:p-10 lg:p-14">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-3 lg:hidden"><svg class="h-11 w-11" viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="14" fill="#1d362b"/><path d="m8 31 9-13 6 8 6-12 11 17H8Z" fill="#f8f4eb"/><path d="m13 31 7-7 4 5 6-8 7 10H13Z" fill="#d78a18"/></svg><strong class="font-display text-xl font-semibold text-brand-950">Pahadi Roots</strong></a>
                <p class="eyebrow">Administrator access</p><h2 class="font-display mt-3 text-4xl font-semibold text-brand-950">Welcome back</h2><p class="mt-3 text-sm leading-6 text-stone-600">Sign in with an authorised staff account. A verification code will be sent to the account email.</p>
                @if($errors->any())<div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>@endif
                <form method="POST" action="{{ route('admin.login.store') }}" class="mt-7 space-y-5">@csrf
                    <div><label for="adminEmail" class="field-label">Administrator email</label><input id="adminEmail" name="email" type="email" value="{{ old('email') }}" autocomplete="username" maxlength="255" class="field-control" required autofocus></div>
                    <div><div class="flex items-center justify-between"><label for="adminPassword" class="field-label">Password</label><a href="{{ route('password.request') }}" class="mb-2 text-xs font-bold text-brand-700 hover:underline">Forgot password?</a></div><input id="adminPassword" name="password" type="password" autocomplete="current-password" class="field-control" required></div>
                    <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" name="remember" value="1"> Trust this browser between sessions</label>
                    <button type="submit" class="button-primary w-full">Continue securely <span aria-hidden="true">→</span></button>
                </form>
                <div class="mt-7 border-t border-stone-300 pt-6 text-center text-sm text-stone-600">Shopping as a customer? <a href="{{ route('login') }}" class="font-extrabold text-brand-700 hover:underline">Customer sign in</a></div>
            </div>
        </section>
    </main>
</body>
</html>
