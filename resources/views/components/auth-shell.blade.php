@props([
    'eyebrow',
    'title',
    'description' => null,
    'image' => 'assets/img/editorial/misty-tea-mountains.webp',
    'points' => [],
])
<div class="mx-auto grid max-w-5xl overflow-hidden rounded-[2rem] bg-white shadow-[var(--shadow-lg)] ring-1 ring-stone-200/70 md:grid-cols-[.95fr_1.05fr]" data-reveal="scale">
    <aside class="relative isolate hidden min-h-[560px] flex-col justify-between overflow-hidden bg-brand-950 p-10 text-white md:flex">
        <img src="{{ asset($image) }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover opacity-60" width="800" height="1000" data-parallax="0.05">
        <span class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-950 via-brand-950/60 to-brand-950/20" aria-hidden="true"></span>
        <a href="{{ route('home') }}" class="flex items-center gap-2.5"><svg class="h-10 w-10" viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect width="48" height="48" rx="14" fill="#F8F4EB"/><path d="m8 31 9-13 6 8 6-12 11 17H8Z" fill="#1d362b"/><path d="m13 31 7-7 4 5 6-8 7 10H13Z" fill="#D78A18"/></svg><span class="font-display text-xl">Pahadi Roots</span></a>
        <div>
            <p class="eyebrow eyebrow-line !text-marigold-300">{{ $eyebrow }}</p>
            <p class="font-display mt-4 text-4xl font-medium leading-[1.05]">{{ $title }}</p>
            @if($description)<p class="mt-4 text-sm leading-7 text-white/75">{{ $description }}</p>@endif
            @if($points)
                <ul class="mt-7 space-y-3 text-sm text-white/85">
                    @foreach($points as $point)<li class="flex items-center gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-marigold-300 text-brand-950" aria-hidden="true"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m5 12 4 4L19 6"/></svg></span>{{ $point }}</li>@endforeach
                </ul>
            @endif
        </div>
    </aside>
    <section class="p-7 sm:p-12">{{ $slot }}</section>
</div>
