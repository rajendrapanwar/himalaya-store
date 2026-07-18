<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'images'])
            ->when($request->search, fn ($query, $search) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
            ->when($request->category, fn ($query, $category) => $query->where('category_id', $category))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->boolean('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function inventory(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))
            ->orderBy('stock_quantity')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.inventory', compact('products'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $data = $request->validate(['stock_quantity' => 'required|integer|min:0|max:100000']);
        $product->update($data);

        return back()->with('success', "Stock updated for {$product->name}.");
    }

    public function create()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'producer_name' => 'nullable|string|max:255', 'producer_location' => 'nullable|string|max:255', 'batch_number' => 'nullable|string|max:100', 'harvested_at' => 'nullable|date', 'ingredients' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'origin_region' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'status' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'main_image_id' => 'nullable|integer',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer',
            'image_order' => 'nullable|string',
        ]);

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'sku' => $request->sku,
            'producer_name' => $request->producer_name, 'producer_location' => $request->producer_location, 'batch_number' => $request->batch_number, 'harvested_at' => $request->harvested_at, 'ingredients' => $request->ingredients,
            'stock_quantity' => $request->stock_quantity,
            'weight' => $request->weight,
            'origin_region' => $request->origin_region,
            'is_featured' => $request->has('is_featured') ? 1 : 0,
            'status' => $request->status,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_main' => $index === 0 ? 1 : 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'category']);

        $categories = Category::where('status', 1)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name,'.$product->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$product->id,
            'producer_name' => 'nullable|string|max:255', 'producer_location' => 'nullable|string|max:255', 'batch_number' => 'nullable|string|max:100', 'harvested_at' => 'nullable|date', 'ingredients' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'origin_region' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'status' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'main_image_id' => 'nullable|integer',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer',
            'image_order' => 'nullable|string',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'sku' => $request->sku,
            'producer_name' => $request->producer_name, 'producer_location' => $request->producer_location, 'batch_number' => $request->batch_number, 'harvested_at' => $request->harvested_at, 'ingredients' => $request->ingredients,
            'stock_quantity' => $request->stock_quantity,
            'weight' => $request->weight,
            'origin_region' => $request->origin_region,
            'is_featured' => $request->has('is_featured') ? 1 : 0,
            'status' => $request->status,
        ]);

        $removeImages = $product->images()->whereIn('id', $request->input('remove_image_ids', []))->get();
        foreach ($removeImages as $removeImage) {
            Storage::disk('public')->delete($removeImage->image);
            $removeImage->delete();
        }

        $orderedIds = collect(explode(',', (string) $request->input('image_order')))->filter()->map(fn ($id) => (int) $id)->values();
        foreach ($orderedIds as $position => $imageId) {
            $product->images()->whereKey($imageId)->update(['sort_order' => $position]);
        }

        $mainImageId = $request->integer('main_image_id');
        if ($mainImageId && $product->images()->whereKey($mainImageId)->exists()) {
            $product->images()->update(['is_main' => false]);
            $product->images()->whereKey($mainImageId)->update(['is_main' => true]);
        }

        if ($request->hasFile('images')) {
            $hasMainImage = $product->images()->where('is_main', 1)->exists();
            $nextOrder = ((int) $product->images()->max('sort_order')) + 1;

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_main' => (! $hasMainImage && $index === 0) ? 1 : 0,
                    'sort_order' => $nextOrder + $index,
                ]);
            }
        }

        if (! $product->images()->where('is_main', true)->exists()) {
            $product->images()->first()?->update(['is_main' => true]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->load('images');

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
