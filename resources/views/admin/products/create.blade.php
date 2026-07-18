@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')

<div class="rounded-2xl border border-stone-200 bg-white shadow-sm">
    <div class="px-8 py-5 border-b">
        <h2 class="text-xl font-bold">Create Product</h2>
        <p class="text-sm text-stone-500">Add a complete, traceable product to the storefront.</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf

        @include('admin.products.form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="button-secondary">
                Cancel
            </a>

            <button class="button-primary">
                Save Product
            </button>
        </div>
    </form>
</div>

@endsection
