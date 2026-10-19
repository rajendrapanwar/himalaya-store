@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="space-y-6">
    <div class="admin-page-intro">
        <div><p class="eyebrow">Catalogue</p><h2>Product library</h2><p>Manage product information, pricing, visibility and stock from one place.</p></div>
        <a href="{{ route('admin.products.create') }}" class="admin-action admin-action-primary"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>Add product</a>
    </div>

    <form method="GET" class="admin-filter-bar lg:grid-cols-[minmax(260px,1fr)_220px_180px_auto_auto]" aria-label="Filter products">
        <div class="relative"><label for="productSearch" class="sr-only">Search products</label><svg class="pointer-events-none absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg><input id="productSearch" name="search" value="{{ request('search') }}" placeholder="Search product name or SKU…" class="w-full rounded-xl !pl-10 pr-4 py-3"></div>
        <div><label for="productCategory" class="sr-only">Category</label><select id="productCategory" name="category" class="w-full rounded-xl px-4 py-3"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div><label for="productStatus" class="sr-only">Status</label><select id="productStatus" name="status" class="w-full rounded-xl px-4 py-3"><option value="">All statuses</option><option value="1" @selected(request('status') === '1')>Active</option><option value="0" @selected(request('status') === '0')>Inactive</option></select></div>
        <button class="admin-action admin-action-primary" type="submit">Apply filters</button>
        <a href="{{ route('admin.products.index') }}" class="admin-action admin-action-secondary">Reset</a>
    </form>

    <section class="admin-panel">
        <header class="admin-panel-header"><div><h3 class="font-black text-brand-950">All products</h3><p class="mt-1 text-xs text-stone-500">{{ $products->total() }} {{ Str::plural('product', $products->total()) }} in the catalogue</p></div><a href="{{ route('admin.inventory.index') }}" class="text-xs font-black text-brand-700">Open inventory →</a></header>
        <div class="overflow-x-auto">
            <table>
                <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Available stock</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                @forelse($products as $product)
                    @php($displayImage = $product->images->firstWhere('is_main', true) ?? $product->images->first())
                    <tr>
                        <td><div class="flex min-w-[280px] items-center gap-3">@if($displayImage)<img src="{{ asset('storage/'.$displayImage->image) }}" alt="" class="h-14 w-14 shrink-0 rounded-xl border border-stone-200 object-cover">@else<div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-300"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M4 19h16M6 17l5-8 3 4 2-3 3 7H6Z"/></svg></div>@endif<div><a href="{{ route('admin.products.edit', $product) }}" class="font-black text-stone-900 hover:text-brand-700">{{ $product->name }}</a><p class="mt-1 font-mono text-[11px] text-stone-500">{{ $product->sku ?: 'No SKU' }}</p></div></div></td>
                        <td class="text-sm text-stone-600">{{ $product->category->name ?? 'Uncategorised' }}</td>
                        <td>@if($product->sale_price)<strong>₹{{ number_format($product->sale_price, 2) }}</strong><small class="ml-1 text-stone-400 line-through">₹{{ number_format($product->price, 2) }}</small>@else<strong>₹{{ number_format($product->price, 2) }}</strong>@endif</td>
                        <td><span class="admin-badge {{ $product->available_stock <= 0 ? 'bg-red-50 text-red-700' : ($product->available_stock <= 10 ? 'bg-amber-50 text-amber-800' : 'bg-stone-100 text-stone-700') }}">{{ $product->available_stock }} available</span></td>
                        <td><span class="admin-badge {{ $product->status ? 'bg-brand-50 text-brand-800' : 'bg-stone-100 text-stone-500' }}"><span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $product->status ? 'bg-brand-600' : 'bg-stone-400' }}"></span>{{ $product->status ? 'Active' : 'Hidden' }}</span></td>
                        <td><div class="flex justify-end gap-2"><a href="{{ route('admin.products.edit', $product) }}" class="admin-action admin-action-secondary !min-h-9 !px-3">Edit</a><form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product permanently?')">@csrf @method('DELETE')<button class="admin-action admin-action-danger !min-h-9 !px-3">Delete</button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-16 text-center"><div class="mx-auto max-w-sm"><strong class="text-lg text-brand-950">No products found</strong><p class="mt-2 text-sm text-stone-500">Try changing the filters or add a new product.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())<div class="border-t border-stone-200 p-4">{{ $products->links() }}</div>@endif
    </section>
</div>
@endsection
