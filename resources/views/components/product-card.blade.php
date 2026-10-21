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
    $stock = $product->available_stock;
@endphp
<article class="product-card group" data-product-card>
    <div class="relative">
        <a href="{{ route('products.show', $product->slug) }}" class="product-media" aria-label="View {{ $product->name }}" data-product-media>
            @if($image)
                <img src="{{ asset('storage/'.$image->image) }}" data-image-fallback alt="{{ $product->name }}" loading="lazy" decoding="async" width="600" height="750">
                @if($alternateImage)<img src="{{ asset('storage/'.$alternateImage->image) }}" data-image-fallback alt="" class="media-alt" loading="lazy" decoding="async" width="600" height="750">@endif
            @else
                <span class="absolute inset-0 flex flex-col items-center justify-center gap-3 px-6 text-center opacity-60">
                    <svg class="h-14 w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/></svg>
                    <span class="text-xs font-black uppercase tracking-widest">Image coming soon</span>
                </span>
            @endif
        </a>
        <div class="pointer-events-none absolute left-3 top-3 z-[2] flex flex-col items-start gap-1.5">
            @if($product->sale_price)<span class="badge badge-sale">−{{ $discount }}%</span>@endif
            @if($product->is_featured)<span class="badge badge-featured">Bestseller</span>@endif
            @if($stock > 0 && $stock <= 5)<span class="badge badge-low">Only {{ $stock }} left</span>@elseif($stock < 1)<span class="badge badge-soldout">Sold out</span>@endif
        </div>
        <div class="card-actions">
            @auth
                <form method="POST" action="{{ $saved ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" data-wishlist-form data-product-id="{{ $product->id }}" data-add-url="{{ route('wishlist.store', $product) }}" data-remove-url="{{ route('wishlist.destroy', $product) }}" data-product-name="{{ $product->name }}" data-remove-card="{{ request()->routeIs('wishlist.index') ? 'true' : 'false' }}">@csrf @if($saved) @method('DELETE') @endif<button type="submit" data-wishlist-button aria-pressed="{{ $saved ? 'true' : 'false' }}" class="card-icon disabled:cursor-wait disabled:opacity-70" aria-label="{{ $saved ? 'Remove '.$product->name.' from wishlist' : 'Add '.$product->name.' to wishlist' }}"><svg data-wishlist-icon class="h-[18px] w-[18px]" fill="{{ $saved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></button></form>
            @else
                <a href="{{ route('login') }}" class="card-icon" aria-label="Sign in to save {{ $product->name }}"><svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></a>
            @endauth
            <button type="button" class="card-icon" data-share data-share-title="{{ $product->name }}" data-share-text="{{ Str::limit($product->short_description ?: 'Discover '.$product->name.' from Pahadi Roots.', 120) }}" data-share-url="{{ route('products.show', $product->slug) }}" aria-label="Share {{ $product->name }}"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke-width="1.9"/><circle cx="6" cy="12" r="2.5" stroke-width="1.9"/><circle cx="18" cy="19" r="2.5" stroke-width="1.9"/><path stroke-width="1.9" d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></button>
        </div>
        @if($stock > 0)
            <form method="POST" action="{{ route('cart.add', $product) }}" class="quick-add" data-add-to-cart data-product-name="{{ $product->name }}">@csrf<input type="hidden" name="quantity" value="1"><button type="submit" class="add-to-cart-chip" aria-label="Add {{ $product->name }} to cart"><svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2.6" d="M12 5v14M5 12h14"/></svg><span class="chip-label" aria-hidden="true">Add to cart</span></button></form>
        @endif
    </div>
    <div class="flex flex-1 flex-col pt-4">
        <p class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[.16em] opacity-60"><span class="truncate">{{ $product->category->name ?? 'Mountain produce' }}</span>@if($product->origin_region)<span aria-hidden="true">·</span><span class="truncate">{{ Str::before($product->origin_region, ',') }}</span>@endif</p>
        <a href="{{ route('products.show', $product->slug) }}" class="mt-2"><h3 class="font-display text-xl font-semibold leading-[1.1] tracking-tight transition-colors group-hover:text-buransh sm:text-[1.45rem]">{{ $product->name }}</h3></a>
        @if($product->short_description)<p class="mt-2 line-clamp-2 text-[13px] leading-5 opacity-70">{{ $product->short_description }}</p>@endif
        <div class="mt-auto flex flex-wrap items-end justify-between gap-2 pt-4">
            <p class="leading-none"><span class="text-lg font-black tracking-tight">₹{{ number_format((float) $price, 2) }}</span>@if($product->sale_price)<span class="ml-2 text-xs opacity-50 line-through">₹{{ number_format((float) $product->price, 2) }}</span>@endif</p>
            @if($reviewCount)
                <p class="flex items-center gap-1.5 text-xs" aria-label="Rated {{ number_format($rating, 1) }} out of 5 from {{ $reviewCount }} reviews"><span class="rating-stars" style="--rating: {{ $rating }}" aria-hidden="true"></span><span class="opacity-60">({{ $reviewCount }})</span></p>
            @endif
        </div>
    </div>
</article>
