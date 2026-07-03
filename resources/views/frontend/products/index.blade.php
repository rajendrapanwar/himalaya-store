@extends('layouts.app')

@section('content')

<div class="space-y-12">
    {{-- Hero Section with Parallax Effect --}}
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center"></div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>

        <div class="relative px-8 py-20 md:px-14 md:py-28 max-w-4xl">
            <div class="animate-fade-in-up">
                <span class="inline-flex items-center rounded-full bg-white/10 backdrop-blur-sm px-5 py-2 text-sm font-medium ring-1 ring-white/30 shadow-lg">
                    🌄 Pure Himalayan Marketplace
                </span>

                <h1 class="mt-8 text-5xl md:text-7xl font-black leading-tight tracking-tight">
                    Natural goods from <span class="bg-gradient-to-r from-emerald-400 to-teal-500 bg-clip-text text-transparent">the mountains</span>
                </h1>

                <p class="mt-6 max-w-2xl text-lg md:text-xl text-stone-200 leading-relaxed">
                    Discover honey, herbs, tea, spices, grains and handmade products sourced from Himachal, Garhwal, Uttarakhand and Himalayan regions.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#products" class="group rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-4 font-semibold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                        Shop Products
                        <span class="inline-block ml-2 transition-transform group-hover:translate-x-1">→</span>
                    </a>

                    <a href="/" class="rounded-full bg-white/10 backdrop-blur-sm px-8 py-4 font-semibold text-white ring-1 ring-white/30 transition-all duration-300 hover:bg-white/20 hover:scale-105">
                        Our Story
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Enhanced Filter Bar with Glassmorphism --}}
    <section class="sticky top-4 z-20 rounded-2xl border border-white/20 bg-white/80 p-5 shadow-xl backdrop-blur-xl transition-all duration-300">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-5 relative">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search honey, tea, spices, rajma..."
                    class="w-full rounded-xl border-0 bg-stone-50/80 pl-12 pr-5 py-4 text-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300"
                >
            </div>

            <div class="md:col-span-4">
                <select
                    name="category"
                    class="w-full rounded-xl border-0 bg-stone-50/80 px-5 py-4 text-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300 cursor-pointer"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3 flex gap-3">
                <button class="flex-1 rounded-xl bg-gradient-to-r from-stone-800 to-stone-900 px-5 py-4 text-sm font-semibold text-white hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105">
                    Apply Filters
                </button>

                <a href="{{ route('products.index') }}"
                   class="rounded-xl border border-stone-200 bg-white/50 px-5 py-4 text-sm font-semibold hover:bg-stone-100 transition-all duration-300">
                    Reset
                </a>
            </div>
        </form>
    </section>

    {{-- Modern Category Pills with Animation --}}
    <section class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide">
        <a href="{{ route('products.index') }}"
           class="group whitespace-nowrap rounded-full px-6 py-2.5 text-sm font-semibold transition-all duration-300 transform hover:scale-105 {{ !request('category') ? 'bg-gradient-to-r from-stone-800 to-stone-900 text-white shadow-lg' : 'bg-white border border-stone-200 text-stone-700 hover:shadow-md' }}">
            All Products
        </a>

        @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
               class="group whitespace-nowrap rounded-full px-6 py-2.5 text-sm font-semibold transition-all duration-300 transform hover:scale-105 {{ request('category') == $category->slug ? 'bg-gradient-to-r from-stone-800 to-stone-900 text-white shadow-lg' : 'bg-white border border-stone-200 text-stone-700 hover:shadow-md' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </section>

    {{-- Products Section with Enhanced Cards --}}
    <section id="products">
        <div class="mb-8 flex items-end justify-between border-b border-stone-200 pb-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-emerald-600">
                    ✨ Premium Collection
                </p>
                <h2 class="text-4xl font-black text-stone-900 mt-1">
                    Mountain Essentials
                </h2>
            </div>

            <div class="hidden md:flex items-center gap-2">
                <div class="h-8 w-0.5 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></div>
                <p class="text-sm font-medium text-stone-500">
                    {{ $products->total() }} premium products
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

            @forelse($products as $product)

                @php
                    $displayImage = $product->images->where('is_main', 1)->first() ?? $product->images->first();
                    $displayPrice = $product->sale_price ?? $product->price;
                @endphp

                <div class="group relative overflow-hidden rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                        {{-- Image Container --}}
                        <div class="relative aspect-[4/5] overflow-hidden bg-gradient-to-br from-stone-100 to-stone-200">
                            @if($displayImage)
                                <img
                                    src="{{ asset('storage/' . $displayImage->image) }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg class="w-20 h-20 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            {{-- Badges --}}
                            <div class="absolute left-4 top-4 flex flex-col gap-2">
                                @if($product->is_featured)
                                    <span class="rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 px-3 py-1.5 text-xs font-bold text-white shadow-lg animate-pulse">
                                        ⭐ Featured
                                    </span>
                                @endif

                                @if($product->sale_price)
                                    <span class="rounded-full bg-gradient-to-r from-amber-400 to-orange-500 px-3 py-1.5 text-xs font-bold text-white shadow-lg">
                                        🔥 Sale
                                    </span>
                                @endif
                            </div>

                            {{-- Quick View Overlay --}}
                            <div class="absolute inset-0 bg-black/60 opacity-0 transition-all duration-300 group-hover:opacity-100 flex items-center justify-center">
                                <div class="transform translate-y-4 opacity-0 transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100">
                                    <div class="rounded-full bg-white px-6 py-3 text-sm font-bold text-stone-900 shadow-xl hover:bg-emerald-500 hover:text-white transition-all duration-300">
                                        Quick View →
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Product Info --}}
                        <div class="p-5">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">
                                    {{ $product->category->name ?? 'Product' }}
                                </p>

                                @if($product->origin_region)
                                    <p class="text-xs text-stone-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $product->origin_region }}
                                    </p>
                                @endif
                            </div>

                            <h3 class="line-clamp-2 text-xl font-bold text-stone-900 group-hover:text-emerald-600 transition-colors duration-300">
                                {{ $product->name }}
                            </h3>

                            @if($product->short_description)
                                <p class="mt-2 line-clamp-2 text-sm text-stone-500 leading-relaxed">
                                    {{ $product->short_description }}
                                </p>
                            @endif

                            <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4">
                                <div>
                                    <span class="text-2xl font-black text-stone-900">
                                        ₹{{ number_format($displayPrice, 2) }}
                                    </span>

                                    @if($product->sale_price)
                                        <span class="ml-2 text-sm text-stone-400 line-through">
                                            ₹{{ number_format($product->price, 2) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($product->stock_quantity > 0)
                                        <span class="flex h-2 w-2">
                                            <span class="animate-ping absolute h-2 w-2 rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        <span class="text-xs font-medium text-emerald-600">In Stock</span>
                                    @else
                                        <span class="text-xs font-medium text-red-500">Sold Out</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            @empty

                <div class="col-span-full rounded-2xl border-2 border-dashed border-stone-200 bg-gradient-to-br from-stone-50 to-white p-20 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-2xl font-bold text-stone-900 mb-2">
                            No products found
                        </h3>
                        <p class="text-stone-500 mb-6">
                            Try changing your search or category filter to discover amazing products.
                        </p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-6 py-3 text-white font-semibold hover:bg-emerald-600 transition-all duration-300">
                            Clear all filters
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </a>
                    </div>
                </div>

            @endforelse

        </div>

        {{-- Enhanced Pagination --}}
        <div class="mt-12">
            @if($products->hasPages())
                <div class="rounded-2xl bg-white p-6 shadow-lg">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>

</div>

{{-- Custom CSS for animations and scrollbar --}}
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

@endsection