{{--
    The Pahadi Roots logo (emblem + name), one source for every page.

    variant: "light" for light backgrounds, "dark" for dark ones.
    tagline: true adds "Pure · Natural · From the hills" under the name.
    Set the height with a class (for example class="h-10"); the width follows.

    The files in public/assets/img/brand/ are generated from the original
    artwork, public/assets/img/logo.png.
--}}
@props(['variant' => 'light', 'tagline' => false, 'alt' => 'Pahadi Roots'])
@php
    $variant = $variant === 'dark' ? 'dark' : 'light';
    $file = ($tagline ? 'logo-tagline-' : 'logo-').$variant;
    // [1x height, 2x height, width at 1x] of the generated files.
    [$small, $large, $width] = match ($file) {
        'logo-light' => [64, 128, 306],
        'logo-dark' => [64, 128, 296],
        'logo-tagline-light' => [96, 192, 427],
        default => [96, 192, 412],
    };
    $base = asset('assets/img/brand/'.$file);
@endphp
<img src="{{ $base }}-{{ $small }}.webp" srcset="{{ $base }}-{{ $small }}.webp 1x, {{ $base }}-{{ $large }}.webp 2x" width="{{ $width }}" height="{{ $small }}" alt="{{ $alt }}" {{ $attributes->class(['w-auto max-w-full object-contain']) }}>
