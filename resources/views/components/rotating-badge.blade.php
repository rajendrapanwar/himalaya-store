@props(['text' => 'Single origin • Small batch • Hand packed • ', 'size' => '8.5rem', 'color' => '#f5b51b', 'center' => '#13201a'])
{{-- Circular text that rotates around a small mountain mark. Decorative. --}}
@php($pathId = 'badge-path-'.Str::random(6))
<span {{ $attributes->class(['inline-grid place-items-center', 'relative' => ! preg_match('/\b(absolute|fixed|sticky)\b/', (string) $attributes->get('class'))]) }} style="width: {{ $size }}; height: {{ $size }}" aria-hidden="true">
    <svg class="rotating-text absolute inset-0 h-full w-full" viewBox="0 0 120 120">
        <defs><path id="{{ $pathId }}" d="M60 60m-48 0a48 48 0 1 1 96 0a48 48 0 1 1-96 0"/></defs>
        <text fill="{{ $color }}" font-size="11.2" font-weight="800" letter-spacing="3.2" font-family="Manrope Variable, sans-serif"><textPath href="#{{ $pathId }}">{{ Str::upper($text) }}</textPath></text>
    </svg>
    <span class="grid h-[42%] w-[42%] place-items-center rounded-full" style="background: {{ $color }}"><svg class="h-1/2 w-1/2" viewBox="0 0 48 48" fill="none"><path d="m6 34 11-16 7 9 7-14 11 21" stroke="{{ $center }}" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
</span>
