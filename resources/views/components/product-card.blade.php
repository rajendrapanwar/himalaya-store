@props(['product', 'wishlistProductIds' => []])
@php
    $image = $product->images->firstWhere('is_main', true) ?? $product->images->first();
    $price = $product->sale_price ?? $product->price;
    $saved = in_array($product->id, $wishlistProductIds, true);
    $reviewCount = (int) ($product->approved_reviews_count ?? 0);
    $rating = (float) ($product->approved_reviews_avg ?? 0);
@endphp
<article class="product-card group relative flex h-full flex-col">
    <div class="relative">
        <a href="{{ route('products.show', $product->slug) }}" class="product-media block" aria-label="View {{ $product->name }}">
            @if($image)
                <img src="{{ asset('storage/'.$image->image) }}" data-image-fallback alt="{{ $product->name }}" loading="lazy" decoding="async" width="600" height="600">
            @else
                <div class="flex h-full flex-col items-center justify-center gap-3 bg-[#f1eee5] px-6 text-center text-stone-500">
                    <svg class="h-14 w-14 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/><path stroke-linecap="round" stroke-width="1.4" d="M12 5h.01"/></svg>
                    <span class="text-xs font-bold uppercase tracking-widest">Image coming soon</span>
                </div>
            @endif
        </a>
        <div class="absolute left-3 top-3 flex gap-2">
            @if($product->sale_price)<span class="rounded-md bg-saffron px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white">Sale</span>@elseif($product->is_featured)<span class="rounded-md bg-brand-800 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white">Featured</span>@endif
        </div>
        <div class="absolute right-3 top-3 flex gap-2">
            <button type="button" class="grid h-10 w-10 place-items-center rounded-full border border-stone-200 bg-white/95 text-stone-700 shadow-sm hover:text-brand-700" data-share data-share-title="{{ $product->name }}" data-share-text="{{ Str::limit($product->short_description ?: 'Discover '.$product->name.' from Pahadi Roots.', 120) }}" data-share-url="{{ route('products.show', $product->slug) }}" aria-label="Share {{ $product->name }}"><svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.8"/><circle cx="6" cy="12" r="2.5" stroke-width="1.8"/><circle cx="18" cy="19" r="2.5" stroke-width="1.8"/><path stroke-width="1.8" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button>
            @auth
                <form method="POST" action="{{ $saved ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" data-wishlist-form data-product-id="{{ $product->id }}" data-add-url="{{ route('wishlist.store', $product) }}" data-remove-url="{{ route('wishlist.destroy', $product) }}" data-product-name="{{ $product->name }}" data-remove-card="{{ request()->routeIs('wishlist.index') ? 'true' : 'false' }}">@csrf @if($saved) @method('DELETE') @endif<button type="submit" data-wishlist-button aria-pressed="{{ $saved ? 'true' : 'false' }}" class="grid h-10 w-10 cursor-pointer place-items-center rounded-full border border-stone-200 bg-white/95 text-stone-700 shadow-sm hover:text-red-600 disabled:cursor-wait disabled:opacity-70" aria-label="{{ $saved ? 'Remove '.$product->name.' from wishlist' : 'Add '.$product->name.' to wishlist' }}"><svg data-wishlist-icon class="h-5 w-5" fill="{{ $saved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></button></form>
            @else
                <a href="{{ route('login') }}" class="grid h-10 w-10 cursor-pointer place-items-center rounded-full border border-stone-200 bg-white/95 text-stone-700 shadow-sm hover:text-red-600" aria-label="Sign in to save {{ $product->name }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></a>
            @endauth
        </div>
    </div>
    <div class="flex flex-1 flex-col p-4 sm:p-5">
        <div class="flex items-start justify-between gap-3 text-[11px] font-bold uppercase tracking-wider text-brand-700"><span>{{ $product->category->name ?? 'Mountain produce' }}</span>@if($product->origin_region)<span class="max-w-[48%] truncate text-right text-stone-500">{{ $product->origin_region }}</span>@endif</div>
        <a href="{{ route('products.show', $product->slug) }}" class="mt-2"><h3 class="line-clamp-2 text-base font-extrabold leading-snug text-stone-900 group-hover:text-brand-700 sm:text-lg">{{ $product->name }}</h3></a>
        @if($reviewCount)
            <p class="mt-2 flex items-center gap-1.5 text-xs text-stone-500" aria-label="Rated {{ number_format($rating, 1) }} out of 5 from {{ $reviewCount }} reviews"><span class="text-saffron" aria-hidden="true">★</span><strong class="text-stone-700">{{ number_format($rating, 1) }}</strong><span>({{ $reviewCount }})</span></p>
        @else
            <p class="mt-2 text-xs text-stone-500">New arrival</p>
        @endif
        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            <p><span class="text-lg font-black text-stone-950">₹{{ number_format((float) $price, 2) }}</span>@if($product->sale_price)<span class="ml-1 text-xs text-stone-400 line-through">₹{{ number_format((float) $product->price, 2) }}</span>@endif</p>
            @if($product->available_stock > 0)
                <form method="POST" action="{{ route('cart.add', $product) }}">@csrf<input type="hidden" name="quantity" value="1"><button type="submit" class="grid h-10 w-10 cursor-pointer place-items-center rounded-lg bg-brand-800 text-white hover:bg-brand-950" aria-label="Add {{ $product->name }} to cart"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h2l2.3 10h9.9l2-7H7m2 11a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm8 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z"/></svg></button></form>
            @else<span class="text-xs font-bold text-red-600">Sold out</span>@endif
        </div>
    </div>
</article>
