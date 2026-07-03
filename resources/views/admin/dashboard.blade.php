@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-8">

    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-green-700 to-emerald-500 rounded-2xl p-8 text-white shadow">
        <h2 class="text-3xl font-bold mb-2">
            Welcome to Himalaya Store Admin
        </h2>
        <p class="text-green-50">
            Manage your mountain products, categories, orders and customers from one place.
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Total Products</p>
            <h3 class="text-3xl font-bold mt-2">0</h3>
            <p class="text-sm text-green-600 mt-2">Ready for product setup</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Total Categories</p>
            <h3 class="text-3xl font-bold mt-2">0</h3>
            <p class="text-sm text-green-600 mt-2">Organise your store</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Total Orders</p>
            <h3 class="text-3xl font-bold mt-2">0</h3>
            <p class="text-sm text-yellow-600 mt-2">Orders will appear here</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Total Revenue</p>
            <h3 class="text-3xl font-bold mt-2">₹0</h3>
            <p class="text-sm text-gray-500 mt-2">After checkout setup</p>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border p-6">
            <h3 class="text-xl font-bold mb-4">
                Quick Actions
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <a href="{{ route('admin.categories.create') }}"
                   class="block rounded-xl border p-5 hover:bg-gray-50">
                    <div class="text-lg font-semibold">Add Category</div>
                    <p class="text-sm text-gray-500 mt-1">
                        Create product categories like Honey, Tea, Spices and Handmade.
                    </p>
                </a>

                <a href="#"
                   class="block rounded-xl border p-5 hover:bg-gray-50">
                    <div class="text-lg font-semibold">Add Product</div>
                    <p class="text-sm text-gray-500 mt-1">
                        Add new Himalayan products once product CRUD is ready.
                    </p>
                </a>

                <a href="#"
                   class="block rounded-xl border p-5 hover:bg-gray-50">
                    <div class="text-lg font-semibold">View Orders</div>
                    <p class="text-sm text-gray-500 mt-1">
                        Track customer orders after checkout is implemented.
                    </p>
                </a>

                <a href="/"
                   target="_blank"
                   class="block rounded-xl border p-5 hover:bg-gray-50">
                    <div class="text-lg font-semibold">View Storefront</div>
                    <p class="text-sm text-gray-500 mt-1">
                        Open the customer-facing website.
                    </p>
                </a>

            </div>
        </div>

        {{-- Store Setup Progress --}}
        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <h3 class="text-xl font-bold mb-4">
                MVP Progress
            </h3>

            <div class="space-y-4">

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Laravel Setup</span>
                        <span>Done</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full w-full"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Category Module</span>
                        <span>In Progress</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full w-2/3"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Product Module</span>
                        <span>Pending</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gray-400 h-2 rounded-full w-1/6"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Checkout</span>
                        <span>Pending</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gray-400 h-2 rounded-full w-0"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

@endsection