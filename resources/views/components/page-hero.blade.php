@props([
    'eyebrow',
    'title',
    'description' => null,
    'image',
    'imagePosition' => 'center',
    'compact' => false,
])

<header {{ $attributes->class(['page-hero relative isolate overflow-hidden rounded-[2rem] bg-brand-950 text-white', 'min-h-[340px]' => $compact, 'min-h-[440px] sm:min-h-[500px]' => ! $compact]) }}>
    <img src="{{ $image }}" alt="" class="absolute inset-0 -z-30 h-full w-full scale-110 object-cover" style="object-position: {{ $imagePosition }}" width="1400" height="700" fetchpriority="high" data-parallax="0.1">
    <div class="absolute inset-0 -z-20 bg-brand-950/45" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-950 via-brand-950/80 to-brand-950/10" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-950/70 via-transparent to-brand-950/10" aria-hidden="true"></div>
    <svg class="pointer-events-none absolute -bottom-px left-0 -z-10 h-16 w-full text-brand-950/40" viewBox="0 0 1200 80" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 80 140 30l90 30 160-50 130 40 120-30 190 50 110-30 260 40Z"/></svg>

    <div class="flex h-full {{ $compact ? 'min-h-[340px]' : 'min-h-[440px] sm:min-h-[500px]' }} items-center px-7 py-14 sm:px-12 lg:px-16">
        <div class="hero-copy-static max-w-3xl">
            <p class="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[.24em] text-marigold-300 animate-[rise-in_.8s_var(--ease-out-expo)_both]"><span class="h-px w-8 bg-current opacity-70" aria-hidden="true"></span>{{ $eyebrow }}</p>
            <h1 class="font-display mt-6 text-[clamp(2.5rem,5.5vw,4.75rem)] font-medium leading-[1] tracking-[-.03em] text-white" data-split>{{ $title }}</h1>
            @if($description)<p class="mt-6 max-w-2xl text-base leading-8 text-white/80 animate-[rise-in_.9s_var(--ease-out-expo)_.2s_both] sm:text-lg">{{ $description }}</p>@endif
            @isset($meta)<div class="mt-5 text-xs font-semibold text-brand-200">{{ $meta }}</div>@endisset
            @isset($actions)<div class="mt-9 flex flex-wrap gap-3 animate-[rise-in_.9s_var(--ease-out-expo)_.32s_both]">{{ $actions }}</div>@endisset
        </div>
    </div>
</header>
