{{--
    An <img> for a stored upload ("products/x.jpg") or a static asset
    ("assets/img/hero/x.webp"). When smaller WebP copies exist it adds srcset
    and sizes so the browser downloads one that fits; otherwise it is a plain
    image. Pass `sizes` to describe how wide the image is shown, and `full`
    for large slots that should also offer the original upload.
--}}
@props(['path', 'sizes' => '100vw', 'full' => false])
@php
    $variants = app(\App\Services\ImageVariants::class);
    $srcset = $variants->srcset($path, (bool) $full);
@endphp
<img src="{{ $variants->url($path) }}" @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif {{ $attributes }}>
