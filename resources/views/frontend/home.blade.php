@extends('layouts.app')

@section('content')

<div class="space-y-24">

    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 text-white shadow-2xl">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center animate-slow-zoom"></div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>

        <!-- Floating Mountain SVG -->
        <div class="absolute top-20 right-10 animate-float-slow opacity-20">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15 8.5L22 9.5L17 14L18.5 21L12 17.5L5.5 21L7 14L2 9.5L9 8.5L12 2Z"/>
            </svg>
        </div>

        <div class="relative z-20 px-8 py-20 md:px-12 lg:px-16 md:py-28 max-w-5xl">
            <div class="animate-fade-in-up">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-5 py-2.5 text-sm font-semibold ring-1 ring-white/30 shadow-lg mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Authentic Himalayan Marketplace
                </div>

                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black leading-tight tracking-tight">
                    Pure mountain
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-400 to-amber-400 bg-clip-text text-transparent">
                        goodness
                    </span>
                    <br>delivered to you.
                </h1>

                <p class="mt-8 max-w-2xl text-lg md:text-xl text-stone-200 leading-relaxed">
                    Discover natural honey, herbal teas, organic spices, premium grains, and handmade treasures sourced directly from Himachal, Garhwal, Uttarakhand, and the Himalayas.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}"
                       class="group relative inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-4 font-bold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105 overflow-hidden">
                        <span class="relative z-10">Shop Now</span>
                        <svg class="relative z-10 w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>

                    <a href="#categories"
                       class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-8 py-4 font-semibold text-white ring-1 ring-white/30 transition-all duration-300 hover:bg-white/20 hover:scale-105">
                        Explore Categories
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Customer Stats with SVGs -->
                <div class="mt-12 flex flex-wrap gap-8 pt-8 border-t border-white/20">
                    <div class="flex items-center gap-3">
                        <div class="text-2xl">
                            <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-bold">4.9/5</div>
                            <div class="text-sm text-stone-300">Customer Rating</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <div class="font-bold">50+</div>
                            <div class="text-sm text-stone-300">Himalayan Products</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div>
                            <div class="font-bold">Free Shipping</div>
                            <div class="text-sm text-stone-300">On orders ₹500+</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust Badges with SVGs --}}
    <section class="grid grid-cols-1 gap-6 md:grid-cols-4">
        <div class="group rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-stone-100">
            <div class="mb-3 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                </svg>
            </div>
            <h3 class="font-black text-lg text-stone-900">100% Natural</h3>
            <p class="mt-2 text-sm text-stone-500 leading-relaxed">No artificial additives, just pure mountain goodness.</p>
        </div>

        <div class="group rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-stone-100">
            <div class="mb-3 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="font-black text-lg text-stone-900">Himalayan Origin</h3>
            <p class="mt-2 text-sm text-stone-500 leading-relaxed">Directly sourced from local Himalayan communities.</p>
        </div>

        <div class="group rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-stone-100">
            <div class="mb-3 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h3 class="font-black text-lg text-stone-900">Freshly Packed</h3>
            <p class="mt-2 text-sm text-stone-500 leading-relaxed">Packed with care to ensure premium quality.</p>
        </div>

        <div class="group rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-stone-100">
            <div class="mb-3 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="font-black text-lg text-stone-900">Support Local</h3>
            <p class="mt-2 text-sm text-stone-500 leading-relaxed">Every purchase supports Himalayan families.</p>
        </div>
    </section>

    {{-- Featured Categories --}}
    <section id="categories">
        <div class="mb-10 text-center md:text-left">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 mb-4">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                <span class="text-sm font-bold text-emerald-700 uppercase tracking-wider">Shop by Category</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-stone-900">
                Explore <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">mountain favourites</span>
            </h2>
            <p class="mt-3 text-stone-500 max-w-2xl mx-auto md:mx-0">Discover authentic products from the heart of the Himalayas</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-stone-800 to-stone-900 shadow-xl transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">

                    <div class="aspect-[16/10] relative">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}"
                                 class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110 group-hover:opacity-30"
                                 alt="{{ $category->name }}">
                        @else
                            <div class="h-full w-full bg-gradient-to-br from-emerald-600 to-teal-700"></div>
                        @endif
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
                        
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <h3 class="text-2xl font-black mb-2">
                            {{ $category->name }}
                        </h3>

                        @if($category->description)
                            <p class="line-clamp-2 text-sm text-stone-200 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                {{ $category->description }}
                            </p>
                        @endif
                        
                        <div class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-emerald-400 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            Shop Now
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border-2 border-dashed border-stone-200 bg-stone-50 p-16 text-center">
                    <svg class="w-16 h-16 text-stone-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-stone-500">Categories coming soon!</p>
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-8 md:hidden">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full border border-stone-200 px-6 py-3 font-semibold hover:bg-stone-50 transition-all">
                View All Categories
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </section>

    {{-- Featured Products --}}
    <section>
        <div class="mb-10 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-2 mb-4">
                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-sm font-bold text-amber-700 uppercase tracking-wider">Handpicked Collection</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-stone-900">
                    Featured <span class="bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">essentials</span>
                </h2>
                <p class="mt-3 text-stone-500">Our most loved mountain treasures, curated just for you</p>
            </div>

            <a href="{{ route('products.index') }}"
               class="group hidden md:inline-flex items-center gap-2 rounded-full border-2 border-stone-200 bg-white px-6 py-3 font-semibold text-stone-700 transition-all duration-300 hover:border-emerald-500 hover:bg-emerald-50 hover:shadow-md">
                View All Products
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($featuredProducts as $product)
                @php
                    $image = $product->images->where('is_main', 1)->first() ?? $product->images->first();
                    $price = $product->sale_price ?? $product->price;
                    $discount = $product->sale_price ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
                @endphp

                <div class="group relative overflow-hidden rounded-2xl bg-white shadow-lg transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                        <div class="relative aspect-[4/5] overflow-hidden bg-gradient-to-br from-stone-100 to-stone-200">
                            @if($image)
                                <img src="{{ asset('storage/' . $image->image) }}"
                                     class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110"
                                     alt="{{ $product->name }}"
                                     loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <svg class="w-20 h-20 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute left-3 top-3 flex flex-col gap-2">
                                <span class="rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 px-3 py-1.5 text-xs font-bold text-white shadow-lg inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Featured
                                </span>
                                @if($discount)
                                    <span class="rounded-full bg-gradient-to-r from-amber-400 to-orange-500 px-3 py-1.5 text-xs font-bold text-white shadow-lg">
                                        -{{ $discount }}% OFF
                                    </span>
                                @endif
                            </div>

                            <div class="absolute inset-0 bg-black/50 opacity-0 transition-all duration-300 group-hover:opacity-100 flex items-center justify-center">
                                <div class="transform translate-y-4 transition-all duration-500 group-hover:translate-y-0">
                                    <div class="rounded-full bg-white px-6 py-3 text-sm font-bold text-stone-900 shadow-xl hover:bg-emerald-500 hover:text-white transition-all duration-300 inline-flex items-center gap-2">
                                        Quick View
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <p class="text-sm font-semibold text-emerald-600">
                                {{ $product->category->name ?? 'Premium Product' }}
                            </p>

                            <h3 class="mt-1 line-clamp-2 text-lg font-black text-stone-900 group-hover:text-emerald-600 transition-colors duration-300">
                                {{ $product->name }}
                            </h3>

                            @if($product->origin_region)
                                <p class="mt-1 text-xs text-stone-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $product->origin_region }}
                                </p>
                            @endif

                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <span class="text-2xl font-black text-stone-900">
                                        ₹{{ number_format($price, 2) }}
                                    </span>
                                    @if($product->sale_price)
                                        <span class="ml-2 text-sm text-stone-400 line-through">
                                            ₹{{ number_format($product->price, 2) }}
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
                </div>
            @empty
                <div class="col-span-full rounded-2xl border-2 border-dashed border-stone-200 bg-stone-50 p-16 text-center">
                    <svg class="w-16 h-16 text-stone-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <p class="text-stone-500 font-medium">Featured products coming soon!</p>
                    <p class="text-sm text-stone-400 mt-1">Check back later for our handpicked mountain treasures</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Special Offer Banner --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 shadow-xl">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] bg-repeat"></div>
        </div>
        
        <div class="relative z-10 px-8 py-12 md:px-12 md:py-16 text-center text-white">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-sm px-4 py-2 mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                </svg>
                <span class="text-sm font-bold uppercase tracking-wider">Limited Time Offer</span>
            </div>
            <h2 class="text-3xl md:text-5xl font-black mb-4">
                Free Shipping on Orders Over ₹500
            </h2>
            <p class="text-lg text-emerald-50 mb-6 max-w-2xl mx-auto">
                Plus, get an additional 10% off on your first order! Use code: <span class="font-mono font-bold bg-white/20 px-3 py-1 rounded-full">HIMALAYAN10</span>
            </p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-4 font-bold text-emerald-600 transition-all duration-300 hover:shadow-xl hover:scale-105">
                Shop Now
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </section>

    {{-- Story & Values Section --}}
    <section class="grid grid-cols-1 overflow-hidden rounded-2xl bg-white shadow-xl lg:grid-cols-2 border border-stone-100">
        <div class="bg-gradient-to-br from-emerald-600 via-teal-700 to-stone-800 p-10 text-white md:p-14">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-sm px-4 py-2 mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-sm font-bold uppercase tracking-wider">Our Story</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-black leading-tight">
                Bringing the taste of the <span class="text-emerald-200">hills</span> to every home.
            </h2>
            <p class="mt-6 leading-relaxed text-emerald-50">
                Founded with a passion for authentic Himalayan products, we connect you directly with local farmers, 
                artisans, and producers from the majestic mountain regions. Every purchase supports sustainable practices 
                and empowers local communities.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>100% Authentic Products</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Sustainably Sourced</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Supports Local Families</span>
                </div>
            </div>
            <a href="{{ route('products.index') }}" class="mt-10 inline-flex items-center gap-2 rounded-full bg-white px-8 py-4 font-bold text-stone-900 transition-all duration-300 hover:shadow-xl hover:scale-105">
                Start Shopping
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>

        <div class="p-10 md:p-14 bg-gradient-to-br from-stone-50 to-white">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 mb-6">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="text-sm font-bold text-emerald-700 uppercase tracking-wider">By the Numbers</span>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="rounded-2xl bg-white p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <p class="text-4xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">50+</p>
                    <p class="mt-2 text-sm font-semibold text-stone-700">Mountain Products</p>
                    <p class="text-xs text-stone-400 mt-1">Carefully selected</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <p class="text-4xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">20+</p>
                    <p class="mt-2 text-sm font-semibold text-stone-700">Local Partners</p>
                    <p class="text-xs text-stone-400 mt-1">Across the Himalayas</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <p class="text-4xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">100%</p>
                    <p class="mt-2 text-sm font-semibold text-stone-700">Natural Guarantee</p>
                    <p class="text-xs text-stone-400 mt-1">No artificial additives</p>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <p class="text-4xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">24/7</p>
                    <p class="mt-2 text-sm font-semibold text-stone-700">Customer Support</p>
                    <p class="text-xs text-stone-400 mt-1">Always here to help</p>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- Custom Animations --}}
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
    
    @keyframes slowZoom {
        from {
            transform: scale(1);
        }
        to {
            transform: scale(1.1);
        }
    }
    
    @keyframes floatSlow {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    
    .animate-slow-zoom {
        animation: slowZoom 20s ease-in-out infinite alternate;
    }
    
    .animate-float-slow {
        animation: floatSlow 6s ease-in-out infinite;
    }
</style>

@endsection