<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0e2119">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>We will be back shortly | Pahadi Roots</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#f4f1e9] text-stone-900 antialiased">
    <main class="relative flex min-h-screen items-center overflow-hidden px-5 py-12 sm:px-8">
        <div class="pointer-events-none absolute -left-24 top-[-8rem] h-96 w-96 rounded-full bg-brand-200/35 blur-3xl"></div><div class="pointer-events-none absolute -bottom-32 right-[-6rem] h-[28rem] w-[28rem] rounded-full bg-amber-200/40 blur-3xl"></div>
        <section class="relative mx-auto grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-[0_30px_90px_rgba(14,33,25,.14)] lg:grid-cols-[.9fr_1.1fr]">
            <div class="relative min-h-72 overflow-hidden bg-brand-950 p-8 text-white sm:p-12 lg:min-h-[620px]"><div class="absolute inset-0 opacity-25" style="background-image: linear-gradient(140deg, transparent 45%, rgba(215,138,24,.3)), radial-gradient(circle at 20% 20%, #91bda3 0 1px, transparent 2px); background-size: auto, 42px 42px"></div><svg class="relative h-full min-h-56 w-full text-brand-100" viewBox="0 0 440 500" fill="none" aria-hidden="true"><circle cx="220" cy="190" r="105" fill="#1d362b" stroke="#629b7a"/><path d="M48 408 163 218l62 91 58-128 114 227H48Z" fill="#f8f4eb" opacity=".92"/><path d="m88 408 91-118 56 72 52-87 80 133H88Z" fill="#d78a18"/><path d="M67 433c88-35 217-35 309 0" stroke="#91bda3" stroke-width="8" stroke-linecap="round"/><path d="M194 134c39 10 67 37 81 75-37-1-71-16-91-46-9-14-4-25 10-29Z" fill="#91bda3"/><path d="M229 207c-7-42-2-75 18-103" stroke="#f8f4eb" stroke-width="7" stroke-linecap="round"/></svg></div>
            <div class="flex flex-col justify-center p-7 sm:p-12 lg:p-14"><a href="{{ url('/') }}" class="flex items-center gap-3"><svg class="h-11 w-11" viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="14" fill="#1d362b"/><path d="m8 31 9-13 6 8 6-12 11 17H8Z" fill="#f8f4eb"/><path d="m13 31 7-7 4 5 6-8 7 10H13Z" fill="#d78a18"/></svg><span><strong class="font-display block text-xl font-semibold text-brand-950">Pahadi Roots</strong><small class="text-[9px] font-black uppercase tracking-[.2em] text-brand-600">From the hills</small></span></a>
                <p class="eyebrow mt-10">A short pause</p><h1 class="font-display mt-4 text-4xl font-semibold leading-tight text-brand-950 sm:text-5xl">We are tending to the store.</h1><p class="mt-6 text-base leading-8 text-stone-600">{{ $maintenance['message'] }}</p>
                @if($maintenance['expected_return_at'])<div class="mt-7 rounded-xl border border-brand-200 bg-brand-50 p-4"><p class="text-[10px] font-black uppercase tracking-[.15em] text-brand-600">Expected back</p><p class="mt-1 font-extrabold text-brand-950">{{ \Carbon\Carbon::parse($maintenance['expected_return_at'])->timezone('Asia/Kolkata')->format('l, d M Y · h:i A') }} IST</p></div>@endif
                <div class="mt-8 flex items-center gap-3 border-t border-stone-200 pt-6 text-sm text-stone-500"><span class="relative flex h-3 w-3"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-500 opacity-50"></span><span class="relative inline-flex h-3 w-3 rounded-full bg-brand-600"></span></span><span>Please check again shortly. Your account and saved cart remain safe.</span></div>
                <a href="mailto:{{ config('services.support_email') }}" class="mt-6 text-sm font-extrabold text-brand-700 hover:underline">Need help? Contact our team →</a>
            </div>
        </section>
    </main>
</body>
</html>
