@props([
    'eyebrow',
    'title',
    'description' => null,
    'image',
    'imagePosition' => 'center',
    'compact' => false,
])

<header {{ $attributes->class(['page-hero relative isolate overflow-hidden rounded-2xl bg-brand-950 text-white', 'min-h-[330px]' => $compact, 'min-h-[420px] sm:min-h-[470px]' => ! $compact]) }}>
    <img src="{{ $image }}" alt="" class="absolute inset-0 -z-30 h-full w-full object-cover" style="object-position: {{ $imagePosition }}" width="1400" height="700" fetchpriority="high">
    <div class="absolute inset-0 -z-20 bg-brand-950/55" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-950 via-brand-950/85 to-brand-950/25" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-950/65 via-transparent to-brand-950/15" aria-hidden="true"></div>

    <div class="flex h-full {{ $compact ? 'min-h-[330px]' : 'min-h-[420px] sm:min-h-[470px]' }} items-center px-7 py-12 sm:px-11 lg:px-14">
        <div class="max-w-3xl">
            <p class="text-[11px] font-black uppercase tracking-[.18em] text-amber-300">{{ $eyebrow }}</p>
            <h1 class="font-display mt-5 text-4xl font-semibold leading-[1.04] text-white sm:text-5xl lg:text-6xl">{{ $title }}</h1>
            @if($description)<p class="mt-6 max-w-2xl text-base leading-8 text-stone-100 sm:text-lg">{{ $description }}</p>@endif
            @isset($meta)<div class="mt-5 text-xs font-semibold text-brand-200">{{ $meta }}</div>@endisset
            @isset($actions)<div class="mt-8 flex flex-wrap gap-3">{{ $actions }}</div>@endisset
        </div>
    </div>
</header>
