<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BundleController extends Controller
{
    public function index()
    {
        return view('admin.bundles.index', ['bundles' => Bundle::withCount('products')->latest()->paginate(20)]);
    }

    public function create()
    {
        return $this->form(new Bundle);
    }

    public function edit(Bundle $bundle)
    {
        return $this->form($bundle->load('products'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $bundle = Bundle::create($this->data($request));
            $bundle->products()->sync($this->items($request));
        });

        return redirect()->route('admin.bundles.index')->with('success', 'Bundle created.');
    }

    public function update(Request $request, Bundle $bundle)
    {
        DB::transaction(function () use ($request, $bundle) {
            $bundle->update($this->data($request, $bundle));
            $bundle->products()->sync($this->items($request));
        });

        return redirect()->route('admin.bundles.index')->with('success', 'Bundle updated.');
    }

    public function destroy(Bundle $bundle)
    {
        if ($bundle->image) {
            Storage::disk('public')->delete($bundle->image);
        }
        $bundle->delete();

        return back()->with('success', 'Bundle deleted.');
    }

    private function form(Bundle $bundle)
    {
        return view('admin.bundles.form', ['bundle' => $bundle, 'products' => Product::where('status', true)->orderBy('name')->get()]);
    }

    private function data(Request $request, ?Bundle $bundle = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('bundles')->ignore($bundle)],
            'description' => 'nullable|string|max:2000', 'price' => 'required|numeric|gt:0',
            'is_active' => 'required|boolean', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'products' => 'required|array|min:1', 'products.*' => 'exists:products,id',
            'product_quantities' => 'required|array', 'product_quantities.*' => 'nullable|integer|min:1|max:100',
        ]);
        unset($data['products'], $data['product_quantities']);
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('image')) {
            if ($bundle?->image) {
                Storage::disk('public')->delete($bundle->image);
            }
            $data['image'] = $request->file('image')->store('bundles', 'public');
        }

        return $data;
    }

    private function items(Request $request): array
    {
        return collect($request->input('products', []))->mapWithKeys(fn ($id) => [$id => ['quantity' => max(1, (int) $request->input("product_quantities.{$id}", 1))]])->all();
    }
}
