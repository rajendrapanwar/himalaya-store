@props(['fill' => '#f5b51b', 'size' => '7.5rem', 'points' => 24])
{{-- A slowly spinning starburst sticker with a short label, e.g. "Small batch". --}}
@php
    $polygon = collect(range(0, $points - 1))->map(function (int $index) use ($points) {
        $radius = $index % 2 ? 41 : 50;
        $angle = deg2rad($index * (360 / $points) - 90);

        return round(50 + $radius * cos($angle), 2).','.round(50 + $radius * sin($angle), 2);
    })->implode(' ');
@endphp
<span {{ $attributes->class(['sticker']) }} style="--size: {{ $size }}">
    <svg viewBox="0 0 100 100" aria-hidden="true"><polygon points="{{ $polygon }}" fill="{{ $fill }}"/></svg>
    <span>{{ $slot }}</span>
</span>
