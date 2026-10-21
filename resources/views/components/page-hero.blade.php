@props([
    'eyebrow',
    'title',
    'description' => null,
    'image',
    'imagePosition' => 'center',
    'compact' => false,
])

<header {{ $attributes->class(['page-hero relative isolate overflow-hidden rounded-[2rem] bg-night text-paper', 'min-h-[380px]' => $compact, 'min-h-[480px] sm:min-h-[560px]' => ! $compact]) }}>
    <img src="{{ $image }}" alt="" class="absolute inset-0 -z-30 h-full w-full scale-110 object-cover" style="object-position: {{ $imagePosition }}" width="1400" height="700" fetchpriority="high" data-parallax="0.12">
    <div class="absolute inset-0 -z-20 bg-night/50" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-night via-night/80 to-night/5" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#6b3b2c]/50 via-transparent to-transparent" aria-hidden="true"></div>
    <x-rotating-badge class="absolute right-8 top-8 hidden md:inline-grid" size="7.5rem" text="From the hills • Packed in Dehradun • " />

    <div class="flex h-full {{ $compact ? 'min-h-[380px]' : 'min-h-[480px] sm:min-h-[560px]' }} items-center px-7 pb-20 pt-14 sm:px-12 lg:px-16">
        <div class="max-w-3xl">
            <p class="eyebrow text-sun animate-[rise-in_.8s_var(--ease-out-expo)_both]">{{ $eyebrow }}</p>
            <h1 class="display-title mt-6 !text-[clamp(2.6rem,5.6vw,5.2rem)] text-paper" data-split>{{ $title }}</h1>
            @if($description)<p class="lead mt-6 max-w-2xl text-paper animate-[rise-in_.9s_var(--ease-out-expo)_.2s_both]">{{ $description }}</p>@endif
            @isset($meta)<div class="mt-5 text-xs font-semibold text-paper/70">{{ $meta }}</div>@endisset
            @isset($actions)<div class="mt-9 flex flex-wrap gap-3 animate-[rise-in_.9s_var(--ease-out-expo)_.32s_both]">{{ $actions }}</div>@endisset
        </div>
    </div>
</header>
