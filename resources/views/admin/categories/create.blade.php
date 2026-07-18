@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

        <div class="border-b px-8 py-5">
            <h2 class="text-xl font-bold">
                Create Category
            </h2>
        </div>

        <form
            action="{{ route('admin.categories.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-8"
        >
            @csrf

            @include('admin.categories.form')

            <div class="mt-8 flex justify-end gap-3">

                <a
                    href="{{ route('admin.categories.index') }}"
                    class="px-5 py-3 rounded-xl border"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-brand-800 px-5 py-3 font-bold text-white hover:bg-brand-900"
                >
                    Save Category
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
