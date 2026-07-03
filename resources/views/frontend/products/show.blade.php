@extends('layouts.app')

@section('content')

@php
    $mainImage = $product->images->where('is_main', 1)->first() ?? $product->images->first();
    $displayPrice = $product->sale_price ?? $product->price;
    $discountPercent = $product->sale_price ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
@endphp

<div class="space-y-16">

    {{-- Modern Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm">
        <a href="/" class="flex items-center gap-1 text-stone-500 transition-colors hover:text-emerald-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Home
        </a>
        <span class="text-stone-400">/</span>
        <a href="{{ route('products.index') }}" class="text-stone-500 transition-colors hover:text-emerald-600">Products</a>
        <span class="text-stone-400">/</span>
        <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}" class="text-stone-500 transition-colors hover:text-emerald-600">
            {{ $product->category->name ?? 'All' }}
        </a>
        <span class="text-stone-400">/</span>
        <span class="font-medium text-stone-900">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- Main Product Section with Enhanced Layout --}}
    <section class="grid grid-cols-1 gap-12 lg:grid-cols-2">

        {{-- Gallery with Lightbox Effect --}}
        <div class="space-y-5">
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-stone-100 to-stone-200 shadow-xl">
                @if($mainImage)
                    <img
                        src="{{ asset('storage/' . $mainImage->image) }}"
                        alt="{{ $product->name }}"
                        class="h-[560px] w-full object-cover transition-transform duration-700 group-hover:scale-105 cursor-pointer"
                        onclick="openLightbox('{{ asset('storage/' . $mainImage->image) }}')"
                    >
                @else
                    <div class="flex h-[560px] w-full items-center justify-center">
                        <svg class="w-32 h-32 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif

                {{-- Discount Badge --}}
                @if($product->sale_price)
                    <div class="absolute left-5 top-5">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full blur-lg opacity-50"></div>
                            <span class="relative rounded-full bg-gradient-to-r from-amber-400 to-orange-500 px-4 py-2 text-sm font-bold text-white shadow-lg">
                                -{{ $discountPercent }}% OFF
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="grid grid-cols-5 gap-3">
                    @foreach($product->images as $index => $image)
                        <div class="group relative overflow-hidden rounded-xl border-2 border-stone-200 bg-stone-100 transition-all duration-300 hover:border-emerald-500 hover:shadow-lg cursor-pointer {{ $loop->first ? 'border-emerald-500 ring-2 ring-emerald-500/20' : '' }}"
                             onclick="changeMainImage('{{ asset('storage/' . $image->image) }}', this)">
                            <img
                                src="{{ asset('storage/' . $image->image) }}"
                                alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                class="h-24 w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                loading="lazy"
                            >
                            @if($loop->first && $product->sale_price)
                                <div class="absolute bottom-1 right-1 rounded-full bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                    Main
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product Info with Sticky Header --}}
        <div class="lg:pl-4">
            <div class="sticky top-6 space-y-8">
                {{-- Category and Badges --}}
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2 text-sm font-bold text-white shadow-md">
                        {{ $product->category->name ?? 'Premium Product' }}
                    </span>

                    @if($product->is_featured)
                        <span class="rounded-full bg-gradient-to-r from-amber-400 to-yellow-500 px-4 py-2 text-sm font-bold text-white shadow-md">
                            ⭐ Featured
                        </span>
                    @endif

                    @if($product->stock_quantity <= 10 && $product->stock_quantity > 0)
                        <span class="rounded-full bg-gradient-to-r from-orange-400 to-red-500 px-4 py-2 text-sm font-bold text-white shadow-md">
                            🔥 Only {{ $product->stock_quantity }} left
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-4xl font-black leading-tight text-stone-900 md:text-5xl lg:text-6xl">
                    {{ $product->name }}
                </h1>

                {{-- Origin and Rating --}}
                <div class="flex flex-wrap items-center gap-6">
                    @if($product->origin_region)
                        <div class="flex items-center gap-2 rounded-full bg-stone-100 px-4 py-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-stone-700">
                                Sourced from <span class="font-bold text-stone-900">{{ $product->origin_region }}</span>
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= 4 ? 'text-yellow-400' : 'text-stone-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-stone-500">(128 customer reviews)</span>
                    </div>
                </div>

                {{-- Short Description --}}
                @if($product->short_description)
                    <div class="rounded-2xl bg-gradient-to-r from-stone-50 to-white p-6 border border-stone-100">
                        <p class="text-lg leading-relaxed text-stone-700">
                            {{ $product->short_description }}
                        </p>
                    </div>
                @endif

                {{-- Price Section --}}
                <div class="border-t border-b border-stone-200 py-6">
                    <div class="flex items-end gap-4">
                        <span class="text-5xl font-black text-stone-900">
                            ₹{{ number_format($displayPrice, 2) }}
                        </span>

                        @if($product->sale_price)
                            <span class="text-xl text-stone-400 line-through">
                                ₹{{ number_format($product->price, 2) }}
                            </span>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-bold text-emerald-700">
                                Save ₹{{ number_format($product->price - $product->sale_price, 2) }}
                            </span>
                        @endif
                    </div>

                    @if($product->weight)
                        <p class="mt-2 text-sm text-stone-500">
                            Price per {{ $product->weight }} kg unit
                        </p>
                    @endif
                </div>

                {{-- Product Details Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-gradient-to-br from-stone-50 to-white p-4 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="rounded-full bg-emerald-100 p-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">
                                Availability
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($product->stock_quantity > 0)
                                <div class="flex items-center gap-1">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <p class="font-bold text-emerald-700">In Stock</p>
                                </div>
                                <p class="text-sm text-stone-600">({{ $product->stock_quantity }} units available)</p>
                            @else
                                <p class="font-bold text-red-600">Out of Stock</p>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl bg-gradient-to-br from-stone-50 to-white p-4 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="rounded-full bg-emerald-100 p-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">
                                Weight
                            </p>
                        </div>
                        <p class="font-bold text-stone-900 text-lg">
                            {{ $product->weight ? $product->weight . ' kg' : 'N/A' }}
                        </p>
                    </div>
                </div>

                {{-- Add to Cart Form with Quantity Selector --}}
                <form method="POST" action="{{ route('cart.add', $product) }}" class="space-y-5">
                    @csrf

                    <div class="space-y-3">
                        <label class="text-sm font-semibold text-stone-700">Quantity</label>
                        <div class="flex gap-3">
                            <div class="relative">
                                <input
                                    type="number"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    max="{{ $product->stock_quantity }}"
                                    class="w-32 rounded-xl border-2 border-stone-200 px-5 py-4 text-center font-bold focus:border-emerald-500 focus:ring-emerald-500 transition-all duration-300"
                                    id="quantity-input"
                                >
                                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex flex-col gap-0.5">
                                    <button type="button" onclick="increaseQuantity()" class="text-stone-400 hover:text-emerald-600 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" onclick="decreaseQuantity()" class="text-stone-400 hover:text-emerald-600 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="group flex-1 rounded-xl bg-gradient-to-r from-stone-800 to-stone-900 px-8 py-4 font-bold text-white transition-all duration-300 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:from-stone-800 disabled:hover:to-stone-900"
                                {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}
                            >
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Add to Cart
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Features List --}}
                <div class="space-y-3 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 p-6">
                    <p class="font-bold text-stone-800 mb-3">Why choose this product?</p>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-white p-1.5 shadow-sm">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-stone-700">Authentic mountain-sourced products</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-white p-1.5 shadow-sm">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-stone-700">Freshly packed and quality checked</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-white p-1.5 shadow-sm">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-stone-700">Supports local Himalayan communities</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-white p-1.5 shadow-sm">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-stone-700">Free shipping on orders over ₹500</span>
                        </div>
                    </div>
                </div>

                {{-- Share Section --}}
                <div class="flex items-center gap-4 pt-4">
                    <span class="text-sm text-stone-500">Share this product:</span>
                    <div class="flex gap-2">
                        <a href="#" class="rounded-full bg-stone-100 p-2 transition-colors hover:bg-emerald-100 hover:text-emerald-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="rounded-full bg-stone-100 p-2 transition-colors hover:bg-emerald-100 hover:text-emerald-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="#" class="rounded-full bg-stone-100 p-2 transition-colors hover:bg-emerald-100 hover:text-emerald-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Description with Tabs --}}
    <section class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-stone-200">
            <div class="flex gap-1 px-6 pt-4">
                <button class="tab-button active px-6 py-3 text-sm font-semibold text-stone-900 border-b-2 border-emerald-500 transition-all" data-tab="description">
                    Product Details
                </button>
                <button class="tab-button px-6 py-3 text-sm font-semibold text-stone-500 hover:text-stone-900 transition-all" data-tab="specifications">
                    Specifications
                </button>
                <button class="tab-button px-6 py-3 text-sm font-semibold text-stone-500 hover:text-stone-900 transition-all" data-tab="reviews">
                    Reviews (128)
                </button>
            </div>
        </div>

        <div class="p-8">
            {{-- Description Tab --}}
            <div id="description" class="tab-content active">
                <div class="prose max-w-none">
                    <div class="text-stone-700 leading-relaxed space-y-4">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            {{-- Specifications Tab --}}
            <div id="specifications" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between py-3 border-b border-stone-100">
                            <span class="font-semibold text-stone-600">Product Name</span>
                            <span class="text-stone-900">{{ $product->name }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-stone-100">
                            <span class="font-semibold text-stone-600">Category</span>
                            <span class="text-stone-900">{{ $product->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-stone-100">
                            <span class="font-semibold text-stone-600">Origin Region</span>
                            <span class="text-stone-900">{{ $product->origin_region ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between py-3 border-b border-stone-100">
                            <span class="font-semibold text-stone-600">Weight</span>
                            <span class="text-stone-900">{{ $product->weight ? $product->weight . ' kg' : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-stone-100">
                            <span class="font-semibold text-stone-600">Stock Status</span>
                            <span class="text-stone-900">{{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}</span>
                        </div>
                        <div class="flex justify-between py-3 border-b border-stone-100">
                            <span class="font-semibold text-stone-600">SKU</span>
                            <span class="text-stone-900">{{ $product->sku ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reviews Tab --}}
            <div id="reviews" class="tab-content hidden">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">⭐ 4.8</div>
                    <p class="text-stone-600">Based on 128 customer reviews</p>
                    <button class="mt-6 rounded-full bg-emerald-500 px-6 py-3 text-white font-semibold hover:bg-emerald-600 transition-colors">
                        Write a Review
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Related Products with Enhanced Design --}}
    @if($relatedProducts->count())
        <section>
            <div class="mb-8 flex items-end justify-between border-b border-stone-200 pb-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-emerald-600">
                        You may also like
                    </p>
                    <h2 class="mt-1 text-3xl font-black text-stone-900">
                        Related Products
                    </h2>
                </div>

                <a href="{{ route('products.index') }}" class="group hidden rounded-full border border-stone-200 px-6 py-2.5 text-sm font-semibold transition-all duration-300 hover:bg-stone-100 hover:shadow-md md:flex items-center gap-2">
                    View All
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($relatedProducts as $related)

                    @php
                        $relatedImage = $related->images->where('is_main', 1)->first() ?? $related->images->first();
                        $relatedPrice = $related->sale_price ?? $related->price;
                    @endphp

                    <a href="{{ route('products.show', $related->slug) }}"
                       class="group overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">

                        <div class="relative aspect-[4/5] overflow-hidden bg-gradient-to-br from-stone-100 to-stone-200">
                            @if($relatedImage)
                                <img
                                    src="{{ asset('storage/' . $relatedImage->image) }}"
                                    alt="{{ $related->name }}"
                                    class="h-full w-full object-cover transition duration-700 group-hover:scale-110"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg class="w-16 h-16 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            @if($related->sale_price)
                                <div class="absolute left-3 top-3 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 px-2 py-1 text-xs font-bold text-white shadow-lg">
                                    -{{ round((($related->price - $related->sale_price) / $related->price) * 100) }}%
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="line-clamp-2 font-bold text-stone-900 group-hover:text-emerald-600 transition-colors duration-300">
                                {{ $related->name }}
                            </h3>

                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    <span class="text-xl font-black text-stone-900">
                                        ₹{{ number_format($relatedPrice, 2) }}
                                    </span>
                                    @if($related->sale_price)
                                        <span class="ml-2 text-sm text-stone-400 line-through">
                                            ₹{{ number_format($related->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="rounded-full bg-emerald-100 p-2 opacity-0 transition-all duration-300 group-hover:opacity-100">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>

                @endforeach
            </div>
        </section>
    @endif

</div>

{{-- Lightbox Modal --}}
<div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/95 backdrop-blur-lg items-center justify-center" onclick="closeLightbox()">
    <div class="relative max-w-5xl w-full mx-4">
        <img id="lightbox-img" src="" alt="" class="w-full rounded-2xl shadow-2xl">
        <button onclick="closeLightbox()" class="absolute -top-12 right-0 text-white hover:text-emerald-400 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

{{-- JavaScript for Interactivity --}}
<script>
    // Quantity selector
    function increaseQuantity() {
        const input = document.getElementById('quantity-input');
        if(input && input.value < parseInt(input.max)) {
            input.value = parseInt(input.value) + 1;
        }
    }
    
    function decreaseQuantity() {
        const input = document.getElementById('quantity-input');
        if(input && input.value > parseInt(input.min)) {
            input.value = parseInt(input.value) - 1;
        }
    }
    
    // Image gallery
    function changeMainImage(imageUrl, element) {
        const mainImage = document.querySelector('.group > img');
        if(mainImage) {
            mainImage.src = imageUrl;
        }
        
        // Update active state
        document.querySelectorAll('.grid-cols-5 > div').forEach(div => {
            div.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
        });
        element.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
    }
    
    // Lightbox
    function openLightbox(imageUrl) {
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        if(lightbox && lightboxImg) {
            lightboxImg.src = imageUrl;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        if(lightbox) {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }
    
    // Tabs functionality
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Update active states
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'text-stone-900', 'border-emerald-500');
                btn.classList.add('text-stone-500');
            });
            button.classList.add('active', 'text-stone-900', 'border-emerald-500');
            button.classList.remove('text-stone-500');
            
            // Show selected tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabId).classList.remove('hidden');
        });
    });
    
    // Prevent lightbox close when clicking on image
    document.getElementById('lightbox-img')?.addEventListener('click', (e) => {
        e.stopPropagation();
    });
</script>

<style>
    .tab-button.active {
        border-bottom-width: 2px;
    }
    
    .tab-content {
        transition: all 0.3s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .tab-content.active {
        animation: fadeIn 0.4s ease-out;
    }
</style>

@endsection