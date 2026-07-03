@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border">
    <div class="px-8 py-5 border-b">
        <h2 class="text-xl font-bold">Create Product</h2>
        <p class="text-sm text-gray-500">Add a new Himalayan product to your store.</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf

        @include('admin.products.form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-xl border">
                Cancel
            </a>

            <button class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl">
                Save Product
            </button>
        </div>
    </form>
</div>

@endsection