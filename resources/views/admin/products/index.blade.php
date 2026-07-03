@extends('layouts.admin')

@section('title', 'Products')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

    <div class="p-6 border-b flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold">Products</h2>
            <p class="text-gray-500 text-sm">Manage store products, prices and stock.</p>
        </div>

        <a href="{{ route('admin.products.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl">
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
                            {{ $product->stock_quantity }}
                        </td>

                        <td class="p-4">
                            @if($product->status)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
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

@endsection