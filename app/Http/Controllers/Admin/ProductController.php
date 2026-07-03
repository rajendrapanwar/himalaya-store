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
    public function index()
    {
        $products = Product::with(['category', 'images'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:255|unique:products,name',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lt:price',
            'sku'               => 'nullable|string|max:100',
            'stock_quantity'    => 'required|integer|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'origin_region'     => 'nullable|string|max:255',
            'is_featured'       => 'nullable',
            'status'            => 'required|boolean',
            'images'            => 'nullable|array',
            'images.*'          => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::create([
            'category_id'       => $request->category_id,
            'name'              => $request->name,
            'slug'              => Str::slug($request->name),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price,
            'sku'               => $request->sku,
            'stock_quantity'    => $request->stock_quantity,
            'weight'            => $request->weight,
            'origin_region'     => $request->origin_region,
            'is_featured'       => $request->has('is_featured') ? 1 : 0,
            'status'            => $request->status,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                    'is_main'    => $index === 0 ? 1 : 0,
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
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:255|unique:products,name,' . $product->id,
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lt:price',
            'sku'               => 'nullable|string|max:100',
            'stock_quantity'    => 'required|integer|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'origin_region'     => 'nullable|string|max:255',
            'is_featured'       => 'nullable',
            'status'            => 'required|boolean',
            'images'            => 'nullable|array',
            'images.*'          => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product->update([
            'category_id'       => $request->category_id,
            'name'              => $request->name,
            'slug'              => Str::slug($request->name),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price,
            'sku'               => $request->sku,
            'stock_quantity'    => $request->stock_quantity,
            'weight'            => $request->weight,
            'origin_region'     => $request->origin_region,
            'is_featured'       => $request->has('is_featured') ? 1 : 0,
            'status'            => $request->status,
        ]);

        if ($request->hasFile('images')) {
            $hasMainImage = $product->images()->where('is_main', 1)->exists();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                    'is_main'    => (!$hasMainImage && $index === 0) ? 1 : 0,
                ]);
            }
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
