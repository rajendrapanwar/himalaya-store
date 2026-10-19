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
            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-brand-600 focus:ring-brand-200"
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
            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-brand-600 focus:ring-brand-200"
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

    <section class="rounded-xl border border-stone-200 bg-stone-50 p-5">
        <p class="text-xs font-black uppercase tracking-wider text-brand-700">Search appearance</p>
        <div class="mt-4 space-y-5">
            <div><label for="seoTitle" class="field-label">SEO title</label><input id="seoTitle" name="seo_title" value="{{ old('seo_title', $category->seo_title ?? '') }}" maxlength="70" class="field-control" placeholder="Category name | Pahadi Roots"><p class="field-help">Leave empty to use the category name.</p></div>
            <div><label for="metaDescription" class="field-label">Meta description</label><textarea id="metaDescription" name="meta_description" rows="3" maxlength="170" class="field-control">{{ old('meta_description', $category->meta_description ?? '') }}</textarea><p class="field-help">Summarise this collection in roughly 140–160 characters.</p></div>
            <div><label for="focusKeyword" class="field-label">Primary search phrase</label><input id="focusKeyword" name="focus_keyword" value="{{ old('focus_keyword', $category->focus_keyword ?? '') }}" maxlength="120" class="field-control" placeholder="Example: Himalayan pulses online"><p class="field-help">Used as an editorial target; search engines do not use a meta-keywords tag.</p></div>
        </div>
    </section>

</div>
