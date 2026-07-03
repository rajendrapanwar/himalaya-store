<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-6">

        <div>
            <label class="block text-sm font-semibold mb-2">Product Name</label>
            <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                   class="w-full rounded-xl border-gray-300 px-4 py-3">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">Short Description</label>
            <textarea name="short_description" rows="3"
                      class="w-full rounded-xl border-gray-300 px-4 py-3">{{ old('short_description', $product->short_description ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">Full Description</label>
            <textarea name="description" rows="8"
                      class="w-full rounded-xl border-gray-300 px-4 py-3">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">Product Images</label>
            <input type="file" name="images[]" multiple accept="image/*"
                   class="w-full rounded-xl border border-gray-300 p-3">

            @if(!empty($product) && $product->images && $product->images->count())
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    @foreach($product->images as $image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $image->image) }}"
                                class="w-full h-28 object-cover rounded-xl border">

                            @if($image->is_main)
                                <span class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded">
                                    Main
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <div class="space-y-6">

        <div>
            <label class="block text-sm font-semibold mb-2">Category</label>
            <select name="category_id" class="w-full rounded-xl border-gray-300 px-4 py-3">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">Price</label>
                <input type="number" step="0.01" name="price"
                       value="{{ old('price', $product->price ?? '') }}"
                       class="w-full rounded-xl border-gray-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Sale Price</label>
                <input type="number" step="0.01" name="sale_price"
                       value="{{ old('sale_price', $product->sale_price ?? '') }}"
                       class="w-full rounded-xl border-gray-300 px-4 py-3">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                   class="w-full rounded-xl border-gray-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">Stock Quantity</label>
            <input type="number" name="stock_quantity"
                   value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                   class="w-full rounded-xl border-gray-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">Weight</label>
            <input type="number" step="0.01" name="weight"
                   value="{{ old('weight', $product->weight ?? '') }}"
                   class="w-full rounded-xl border-gray-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">Origin Region</label>
            <input type="text" name="origin_region"
                   placeholder="Himachal, Garhwal, Uttarakhand..."
                   value="{{ old('origin_region', $product->origin_region ?? '') }}"
                   class="w-full rounded-xl border-gray-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2">Status</label>
            <select name="status" class="w-full rounded-xl border-gray-300 px-4 py-3">
                <option value="1" {{ old('status', $product->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $product->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <label class="flex items-center gap-3">
            <input type="checkbox" name="is_featured" value="1"
                   {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
            <span class="font-medium">Featured Product</span>
        </label>

    </div>

</div>