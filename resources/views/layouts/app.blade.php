<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pure Himalayan marketplace - Natural honey, herbs, tea, spices, grains and handmade products from the mountains">
    <meta name="keywords" content="Himalayan products, natural honey, herbal tea, organic spices, mountain goods">
    <meta name="author" content="Himalaya Store">
    
    <title>{{ config('app.name', 'Himalaya Store') }} - Pure Himalayan Marketplace</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Additional custom styles for header/footer */
        .dropdown:hover .dropdown-menu {
            display: block;
            animation: fadeInDown 0.3s ease-out;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cart-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        /* Mobile menu animation */
        .mobile-menu-enter {
            opacity: 0;
            transform: translateY(-20px);
        }
        
        .mobile-menu-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease-out;
        }
        
        .mobile-menu-exit {
            opacity: 1;
            transform: translateY(0);
        }
        
        .mobile-menu-exit-active {
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease-in;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-stone-50 via-white to-stone-50 text-stone-900">

    {{-- Modern Header --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md shadow-lg border-b border-stone-200">
        <!-- Top Bar -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Authentic Himalayan Products</span>
                        </div>
                        <div class="hidden sm:flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            <span>Free Shipping on ₹500+</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <a href="#" class="hover:text-emerald-200 transition-colors flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            Help
                        </a>
                        <a href="#" class="hover:text-emerald-200 transition-colors flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                            </svg>
                            Track Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="/" class="group flex items-center space-x-3">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Product Image" style="height:80px;">
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/" class="relative text-stone-700 hover:text-emerald-600 font-semibold transition-all duration-300 group">
                        Home
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    
                    <div class="relative dropdown">
                        <a href="{{ route('products.index') }}" class="flex items-center gap-1 text-stone-700 hover:text-emerald-600 font-semibold transition-all duration-300 group">
                            Shop
                            <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                        <div class="dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-stone-100 overflow-hidden z-50">
                            <a href="{{ route('products.index') }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                All Products
                            </a>
                            <a href="{{ route('products.index', ['category' => 'honey']) }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                🍯 Honey & Sweeteners
                            </a>
                            <a href="{{ route('products.index', ['category' => 'tea']) }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                🍃 Herbal Teas
                            </a>
                            <a href="{{ route('products.index', ['category' => 'spices']) }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                🌿 Organic Spices
                            </a>
                            <a href="{{ route('products.index', ['category' => 'grains']) }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                🌾 Himalayan Grains
                            </a>
                            <a href="{{ route('products.index', ['category' => 'handmade']) }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                🎨 Handmade Crafts
                            </a>
                        </div>
                    </div>
                    
                    <a href="#" class="text-stone-700 hover:text-emerald-600 font-semibold transition-all duration-300 group relative">
                        About Us
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    
                    <a href="#" class="text-stone-700 hover:text-emerald-600 font-semibold transition-all duration-300 group relative">
                        Blog
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    
                    <a href="#" class="text-stone-700 hover:text-emerald-600 font-semibold transition-all duration-300 group relative">
                        Contact
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </nav>

                <!-- Header Actions -->
                <div class="flex items-center gap-3">
                    <!-- Search Button -->
                    <button id="searchButton" class="p-2 text-stone-600 hover:text-emerald-600 transition-colors rounded-full hover:bg-stone-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    <!-- User Account -->
                    <div class="relative dropdown">
                        <button class="p-2 text-stone-600 hover:text-emerald-600 transition-colors rounded-full hover:bg-stone-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </button>
                        <div class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-stone-100 overflow-hidden z-50">
                            @auth
                                <a href="{{ route('profile') }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                    My Account
                                </a>
                                <a href="{{ route('orders') }}" class="block px-4 py-3 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                    My Orders
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        Logout
                                    </button>
                                </form>
                            @else
                            
                            @endauth
                        </div>
                    </div>

                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-stone-600 hover:text-emerald-600 transition-colors rounded-full hover:bg-stone-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        @php
                            $cartCount = session('cart_count', 0);
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center cart-badge">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuButton" class="md:hidden p-2 text-stone-600 hover:text-emerald-600 transition-colors rounded-lg hover:bg-stone-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Bar (Hidden by default) -->
        <div id="searchBar" class="hidden bg-white border-t border-stone-200 py-4 shadow-inner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('products.index') }}" class="relative">
                    <input type="text" 
                           name="search" 
                           placeholder="Search for honey, tea, spices, grains..." 
                           class="w-full rounded-full border-0 bg-stone-50 pl-12 pr-4 py-3 text-stone-900 placeholder-stone-400 focus:ring-2 focus:ring-emerald-500">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button type="button" id="closeSearch" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-stone-200 shadow-lg">
            <div class="px-4 py-4 space-y-3">
                <a href="/" class="block px-4 py-3 text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors font-medium">
                    Home
                </a>
                <a href="{{ route('products.index') }}" class="block px-4 py-3 text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors font-medium">
                    All Products
                </a>
                <a href="#" class="block px-4 py-3 text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors font-medium">
                    About Us
                </a>
                <a href="#" class="block px-4 py-3 text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors font-medium">
                    Blog
                </a>
                <a href="#" class="block px-4 py-3 text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors font-medium">
                    Contact
                </a>
                <hr class="my-2">
                @auth
                    <a href="{{ route('profile') }}" class="block px-4 py-3 text-stone-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors">
                        My Account
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            Logout
                        </button>
                    </form>
                @else
                    
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    {{-- Modern Footer --}}
    <footer class="bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 text-white mt-20">
        <!-- Main Footer -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full p-2">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L15 8.5L22 9.5L17 14L18.5 21L12 17.5L5.5 21L7 14L2 9.5L9 8.5L12 2Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black">Himalaya Store</h3>
                            <p class="text-xs text-emerald-400">Pure Mountain Goods</p>
                        </div>
                    </div>
                    <p class="text-stone-300 leading-relaxed mb-6">
                        Bringing authentic Himalayan products directly from local farmers, artisans, and producers to your doorstep. Supporting sustainable practices and local communities.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-white/10 rounded-full p-2 hover:bg-emerald-500 transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-white/10 rounded-full p-2 hover:bg-emerald-500 transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-white/10 rounded-full p-2 hover:bg-emerald-500 transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-white/10 rounded-full p-2 hover:bg-emerald-500 transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-black mb-6 relative inline-block">
                        Quick Links
                        <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500"></span>
                    </h4>
                    <ul class="space-y-3">
                        <li><a href="/" class="text-stone-300 hover:text-emerald-400 transition-colors flex items-center gap-2 group">
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Home
                        </a></li>
                        <li><a href="{{ route('products.index') }}" class="text-stone-300 hover:text-emerald-400 transition-colors flex items-center gap-2 group">
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Shop All Products
                        </a></li>
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors flex items-center gap-2 group">
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            About Us
                        </a></li>
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors flex items-center gap-2 group">
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Blog
                        </a></li>
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors flex items-center gap-2 group">
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Contact Us
                        </a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h4 class="text-lg font-black mb-6 relative inline-block">
                        Customer Service
                        <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500"></span>
                    </h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors">Shipping Policy</a></li>
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors">Return Policy</a></li>
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-stone-300 hover:text-emerald-400 transition-colors">Terms & Conditions</a></li>
                    </ul>
                </div>

                <!-- Newsletter & Contact -->
                <div>
                    <h4 class="text-lg font-black mb-6 relative inline-block">
                        Stay Updated
                        <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500"></span>
                    </h4>
                    <p class="text-stone-300 text-sm mb-4">
                        Subscribe to get special offers, new product alerts, and Himalayan stories.
                    </p>
                    <form class="mb-6">
                        <div class="relative">
                            <input type="email" 
                                   placeholder="Your email address" 
                                   class="w-full rounded-full bg-white/10 border border-white/20 px-4 py-3 text-sm text-white placeholder-stone-400 focus:outline-none focus:border-emerald-500">
                            <button class="absolute right-1 top-1 bottom-1 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full px-4 text-sm font-semibold hover:shadow-lg transition-all">
                                Subscribe
                            </button>
                        </div>
                    </form>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3 text-stone-300">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>support@himalayastore.com</span>
                        </div>
                        <div class="flex items-center gap-3 text-stone-300">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>+91 123 456 7890</span>
                        </div>
                        <div class="flex items-center gap-3 text-stone-300">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Himachal Pradesh, India</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="border-t border-white/10 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <span class="text-stone-400 text-sm">Secure Payments:</span>
                        <div class="flex gap-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/196/196578.png" alt="Visa" class="h-8 bg-white rounded p-1">
                            <img src="https://cdn-icons-png.flaticon.com/512/196/196561.png" alt="Mastercard" class="h-8 bg-white rounded p-1">
                            <img src="https://cdn-icons-png.flaticon.com/512/196/196539.png" alt="PayPal" class="h-8 bg-white rounded p-1">
                            <img src="https://cdn-icons-png.flaticon.com/512/196/196566.png" alt="Amex" class="h-8 bg-white rounded p-1">
                        </div>
                    </div>
                    <div class="text-sm text-stone-400">
                        © {{ date('Y') }} Himalaya Store. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Interactive Elements -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        // Search Bar Toggle
        const searchButton = document.getElementById('searchButton');
        const searchBar = document.getElementById('searchBar');
        const closeSearch = document.getElementById('closeSearch');
        
        if (searchButton && searchBar) {
            searchButton.addEventListener('click', () => {
                searchBar.classList.toggle('hidden');
                if (!searchBar.classList.contains('hidden')) {
                    searchBar.querySelector('input').focus();
                }
            });
        }
        
        if (closeSearch && searchBar) {
            closeSearch.addEventListener('click', () => {
                searchBar.classList.add('hidden');
            });
        }
        
        // Close mobile menu when clicking outside (optional)
        document.addEventListener('click', function(event) {
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                const isClickInside = mobileMenu.contains(event.target) || mobileMenuButton.contains(event.target);
                if (!isClickInside) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
        
        // Dropdown menus stay open on hover (CSS handles this)
        // Add touch support for mobile devices
        if ('ontouchstart' in window) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (menu) {
                        // Close other dropdowns
                        dropdowns.forEach(d => {
                            if (d !== dropdown) {
                                d.querySelector('.dropdown-menu')?.classList.remove('show');
                            }
                        });
                        menu.classList.toggle('show');
                    }
                });
            });
            
            // Update CSS for touch devices
            const style = document.createElement('style');
            style.textContent = `
                .dropdown-menu.show {
                    display: block !important;
                }
            `;
            document.head.appendChild(style);
        }
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== "#" && href !== "") {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
        
        // Add active class to current navigation item
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('nav a, .mobile-menu a');
        navLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;
            if (linkPath === currentPath && linkPath !== '/') {
                link.classList.add('text-emerald-600');
                link.classList.remove('text-stone-700');
            } else if (currentPath === '/' && linkPath === '/') {
                link.classList.add('text-emerald-600');
                link.classList.remove('text-stone-700');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>