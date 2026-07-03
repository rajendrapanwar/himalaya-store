@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

    <div class="p-6 border-b flex justify-between items-center">

        <div>
            <h2 class="text-xl font-bold">
                Categories
            </h2>

            <p class="text-gray-500 text-sm">
                Manage your product categories
            </p>
        </div>

        <a
            href="{{ route('admin.categories.create') }}"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl"
        >
            + Add Category
        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-4">Image</th>
                    <th class="text-left p-4">Category</th>
                    <th class="text-left p-4">Slug</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-right p-4">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($categories as $category)

                <tr class="border-t hover:bg-gray-50">

                    <td class="p-4">

                        @if($category->image)
                            <img
                                src="{{ asset('storage/'.$category->image) }}"
                                class="w-16 h-16 rounded-xl object-cover"
                            >
                        @else
                            <div class="w-16 h-16 rounded-xl bg-gray-200"></div>
                        @endif

                    </td>

                    <td class="p-4">

                        <div class="font-semibold">
                            {{ $category->name }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ Str::limit($category->description, 60) }}
                        </div>

                    </td>

                    <td class="p-4 text-gray-600">
                        {{ $category->slug }}
                    </td>

                    <td class="p-4">

                        @if($category->status)
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

                            <a
                                href="{{ route('admin.categories.edit', $category) }}"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('admin.categories.destroy', $category) }}"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    onclick="return confirm('Delete category?')"
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg"
                                >
                                    Delete
                                </button>
                            </form>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="5" class="text-center p-12 text-gray-500">
                        No categories found
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>
@endsection