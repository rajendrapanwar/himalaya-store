@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="space-y-6">
    <div class="admin-page-intro"><div><p class="eyebrow">Catalogue structure</p><h2>Product categories</h2><p>Organise the storefront into clear, discoverable collections.</p></div><a href="{{ route('admin.categories.create') }}" class="admin-action admin-action-primary"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>Add category</a></div>

    <section class="admin-panel">
        <header class="admin-panel-header"><div><h3 class="font-black text-brand-950">Store collections</h3><p class="mt-1 text-xs text-stone-500">{{ $categories->count() }} categories configured</p></div></header>
        <div class="overflow-x-auto"><table><thead><tr><th>Category</th><th>Slug</th><th>Status</th><th class="text-right">Actions</th></tr></thead><tbody>
            @forelse($categories as $category)
                <tr>
                    <td><div class="flex min-w-[320px] items-center gap-3">@if($category->image)<img src="{{ asset('storage/'.$category->image) }}" alt="" class="h-14 w-14 rounded-xl border border-stone-200 object-cover">@else<div class="grid h-14 w-14 place-items-center rounded-xl bg-brand-50 text-brand-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.6" d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/></svg></div>@endif<div><a href="{{ route('admin.categories.edit', $category) }}" class="font-black text-stone-900 hover:text-brand-700">{{ $category->name }}</a><p class="mt-1 max-w-lg text-xs text-stone-500">{{ Str::limit($category->description, 80) ?: 'No description added yet.' }}</p></div></div></td>
                    <td><code class="rounded-md bg-stone-100 px-2 py-1 text-xs text-stone-600">{{ $category->slug }}</code></td>
                    <td><span class="admin-badge {{ $category->status ? 'bg-brand-50 text-brand-800' : 'bg-stone-100 text-stone-500' }}"><span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $category->status ? 'bg-brand-600' : 'bg-stone-400' }}"></span>{{ $category->status ? 'Active' : 'Hidden' }}</span></td>
                    <td><div class="flex justify-end gap-2"><a href="{{ route('admin.categories.edit', $category) }}" class="admin-action admin-action-secondary !min-h-9 !px-3">Edit</a><form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category permanently?')">@csrf @method('DELETE')<button class="admin-action admin-action-danger !min-h-9 !px-3">Delete</button></form></div></td>
                </tr>
            @empty<tr><td colspan="4" class="py-16 text-center"><strong class="text-lg text-brand-950">No categories yet</strong><p class="mt-2 text-sm text-stone-500">Create the first collection for your product catalogue.</p></td></tr>@endforelse
        </tbody></table></div>
    </section>
</div>
@endsection
