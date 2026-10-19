@props(['product', 'wishlistProductIds' => []])
@php
    $images = $product->images->sortByDesc('is_main')->values();
    $image = $images->first();
    $alternateImage = $images->get(1);
    $price = $product->sale_price ?? $product->price;
    $saved = in_array($product->id, $wishlistProductIds, true);
    $reviewCount = (int) ($product->approved_reviews_count ?? 0);
    $rating = (float) ($product->approved_reviews_avg ?? 0);
    $discount = $product->sale_price && (float) $product->price > 0 ? (int) round((1 - (float) $product->sale_price / (float) $product->price) * 100) : 0;
    $inStock = $product->available_stock > 0;
@endphp
<article class="product-card group" data-product-card>
    <div class="relative">
        <a href="{{ route('products.show', $product->slug) }}" class="product-media" aria-label="View {{ $product->name }}" data-product-media>
            @if($image)
                <img src="{{ asset('storage/'.$image->image) }}" data-image-fallback alt="{{ $product->name }}" loading="lazy" decoding="async" width="600" height="600">
                @if($alternateImage)<img src="{{ asset('storage/'.$alternateImage->image) }}" data-image-fallback alt="" class="media-alt" loading="lazy" decoding="async" width="600" height="600">@endif
            @else
                <span class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-parchment px-6 text-center text-stone-500">
                    <svg class="h-14 w-14 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/><path stroke-linecap="round" stroke-width="1.4" d="M12 5h.01"/></svg>
                    <span class="text-xs font-bold uppercase tracking-widest">Image coming soon</span>
                </span>
            @endif
        </a>
        <div class="pointer-events-none absolute left-3 top-3 flex flex-col items-start gap-1.5">
            @if($product->sale_price)<span class="badge badge-sale">Sale{{ $discount ? ' −'.$discount.'%' : '' }}</span>@elseif($product->is_featured)<span class="badge badge-featured">★ Featured</span>@endif
            @unless($inStock)<span class="badge badge-soldout">Sold out</span>@endunless
        </div>
        <div class="card-actions absolute right-3 top-3 flex flex-col gap-2">
            @auth
                <form method="POST" action="{{ $saved ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" data-wishlist-form data-product-id="{{ $product->id }}" data-add-url="{{ route('wishlist.store', $product) }}" data-remove-url="{{ route('wishlist.destroy', $product) }}" data-product-name="{{ $product->name }}" data-remove-card="{{ request()->routeIs('wishlist.index') ? 'true' : 'false' }}">@csrf @if($saved) @method('DELETE') @endif<button type="submit" data-wishlist-button aria-pressed="{{ $saved ? 'true' : 'false' }}" class="card-icon disabled:cursor-wait disabled:opacity-70" aria-label="{{ $saved ? 'Remove '.$product->name.' from wishlist' : 'Add '.$product->name.' to wishlist' }}"><svg data-wishlist-icon class="h-[18px] w-[18px]" fill="{{ $saved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></button></form>
            @else
                <a href="{{ route('login') }}" class="card-icon" aria-label="Sign in to save {{ $product->name }}"><svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></a>
            @endauth
            <button type="button" class="card-icon" data-share data-share-title="{{ $product->name }}" data-share-text="{{ Str::limit($product->short_description ?: 'Discover '.$product->name.' from Pahadi Roots.', 120) }}" data-share-url="{{ route('products.show', $product->slug) }}" aria-label="Share {{ $product->name }}"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.8"/><circle cx="6" cy="12" r="2.5" stroke-width="1.8"/><circle cx="18" cy="19" r="2.5" stroke-width="1.8"/><path stroke-width="1.8" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button>
        </div>
    </div>
    <div class="flex flex-1 flex-col p-4 sm:p-5">
        <div class="flex items-center justify-between gap-3 text-[10px] font-black uppercase tracking-[.14em] text-brand-600"><span class="truncate">{{ $product->category->name ?? 'Mountain produce' }}</span>@if($product->origin_region)<span class="flex min-w-0 max-w-[50%] items-center gap-1 truncate text-stone-400"><svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z"/></svg><span class="truncate">{{ $product->origin_region }}</span></span>@endif</div>
        <a href="{{ route('products.show', $product->slug) }}" class="mt-2"><h3 class="font-display line-clamp-2 text-lg font-medium leading-snug text-ink transition-colors group-hover:text-brand-700 sm:text-xl">{{ $product->name }}</h3></a>
        @if($reviewCount)
            <p class="mt-2 flex items-center gap-2 text-xs text-stone-500" aria-label="Rated {{ number_format($rating, 1) }} out of 5 from {{ $reviewCount }} reviews"><span class="rating-stars" style="--rating: {{ $rating }}" aria-hidden="true"></span><strong class="text-stone-700">{{ number_format($rating, 1) }}</strong><span>({{ $reviewCount }})</span></p>
        @else
            <p class="mt-2 text-xs text-stone-400">New arrival</p>
        @endif
        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            <p class="leading-none"><span class="block text-lg font-black tracking-tight text-ink sm:text-xl">₹{{ number_format((float) $price, 2) }}</span>@if($product->sale_price)<span class="mt-1 block text-xs text-stone-400 line-through">₹{{ number_format((float) $product->price, 2) }}</span>@endif</p>
            @if($inStock)
                <form method="POST" action="{{ route('cart.add', $product) }}" data-add-to-cart data-product-name="{{ $product->name }}">@csrf<input type="hidden" name="quantity" value="1"><button type="submit" class="add-to-cart-chip" aria-label="Add {{ $product->name }} to cart"><svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 8h14l-1 13H6L5 8Zm3 2V6a4 4 0 0 1 8 0v4"/></svg><span class="chip-label" aria-hidden="true">Add</span></button></form>
            @else
                <span class="text-xs font-bold text-red-700">Sold out</span>
            @endif
        </div>
    </div>
</article>
