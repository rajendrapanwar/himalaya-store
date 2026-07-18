@extends('layouts.admin')

@section('title', 'Products')

@section('content')

<div class="space-y-5">
<form method="GET" class="flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-stone-200 md:flex-row">
    <input name="search" value="{{ request('search') }}" placeholder="Search name or SKU…" class="flex-1 rounded-xl border-stone-200 px-4 py-3">
    <select name="category" class="rounded-xl border-stone-200 px-4 py-3"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select>
    <select name="status" class="rounded-xl border-stone-200 px-4 py-3"><option value="">All statuses</option><option value="1" @selected(request('status') === '1')>Active</option><option value="0" @selected(request('status') === '0')>Inactive</option></select>
    <button class="rounded-xl bg-stone-900 px-5 py-3 font-bold text-white">Filter</button><a href="{{ route('admin.products.index') }}" class="rounded-xl px-4 py-3 font-bold text-stone-600">Reset</a>
</form>
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

    <div class="p-6 border-b flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold">Products</h2>
            <p class="text-gray-500 text-sm">Manage store products, prices and stock.</p>
        </div>

        <a href="{{ route('admin.products.create') }}"
           class="rounded-xl bg-brand-800 px-5 py-3 font-bold text-white hover:bg-brand-900">
            + Add Product
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-4">Product</th>
                    <th class="text-left p-4">Category</th>
                    <th class="text-left p-4">Price</th>
                    <th class="text-left p-4">Stock</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-right p-4">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $product)
                    <tr class="border-t hover:bg-gray-50">

                        <td class="p-4">
                            <div class="flex items-center gap-4">
                               @php
                                    $displayImage = $product->images->where('is_main', 1)->first() ?? $product->images->first();
                                @endphp

                                @if($displayImage)
                                    <img src="{{ asset('storage/' . $displayImage->image) }}"
                                        class="w-16 h-16 rounded-xl object-cover border">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-gray-200"></div>
                                @endif

                                <div>
                                    <div class="font-semibold">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->sku ?? 'No SKU' }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="p-4">
                            {{ $product->category->name ?? '-' }}
                        </td>

                        <td class="p-4">
                            @if($product->sale_price)
                                <div class="font-semibold">₹{{ number_format($product->sale_price, 2) }}</div>
                                <div class="text-sm line-through text-gray-400">₹{{ number_format($product->price, 2) }}</div>
                            @else
                                <div class="font-semibold">₹{{ number_format($product->price, 2) }}</div>
                            @endif
                        </td>

                        <td class="p-4">
                            <span class="font-bold {{ $product->stock_quantity <= 10 ? 'text-amber-700' : '' }}">{{ $product->stock_quantity }}</span>
                        </td>

                        <td class="p-4">
                            @if($product->status)
                                <span class="rounded-full bg-brand-100 px-3 py-1 text-sm font-bold text-brand-700">
                                    Active
                                </span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="p-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="px-3 py-2 bg-blue-600 text-white rounded-lg">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm('Delete product?')"
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-12 text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">
        {{ $products->links() }}
    </div>

</div>
</div>

@endsection
