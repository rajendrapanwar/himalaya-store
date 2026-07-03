@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border">
    <div class="px-8 py-5 border-b">
        <h2 class="text-xl font-bold">Edit Product</h2>
        <p class="text-sm text-gray-500">Update product details, pricing and stock.</p>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf
        @method('PUT')

        @include('admin.products.form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-xl border">
                Cancel
            </a>

            <button class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl">
                Update Product
            </button>
        </div>
    </form>
</div>

@endsection