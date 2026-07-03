<div class="space-y-6">

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Category Name
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            placeholder="Enter category name"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500"
        >

        @error('name')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Description
        </label>

        <textarea
            name="description"
            rows="5"
            placeholder="Category description..."
            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500"
        >{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Category Image
        </label>

        <input
            type="file"
            name="image"
            accept="image/*"
            class="block w-full text-sm border rounded-xl p-3"
        >

        @if(!empty($category->image))
            <div class="mt-4">
                <img
                    src="{{ asset('storage/'.$category->image) }}"
                    class="w-32 h-32 rounded-xl object-cover border"
                >
            </div>
        @endif

        @error('image')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Status
        </label>

        <select
            name="status"
            class="w-full rounded-xl border border-gray-300 px-4 py-3"
        >
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>

</div>