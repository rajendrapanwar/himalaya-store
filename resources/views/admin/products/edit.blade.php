@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')

<div class="rounded-2xl border border-stone-200 bg-white shadow-sm">
    <div class="px-8 py-5 border-b">
        <h2 class="text-xl font-bold">Edit Product</h2>
        <p class="text-sm text-stone-500">Update product details, media, traceability, pricing and stock.</p>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf
        @method('PUT')

        @include('admin.products.form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="button-secondary">
                Cancel
            </a>

            <button class="button-primary">
                Update Product
            </button>
        </div>
    </form>
</div>

@endsection
