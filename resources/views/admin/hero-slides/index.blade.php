@extends('layouts.admin')
@section('title', 'Homepage slider')
@section('content')
<div class="space-y-6">
    <div class="admin-page-intro">
        <div><p class="eyebrow">Storefront marketing</p><h2>Hero slides</h2><p class="max-w-2xl">Control homepage campaigns, links, display order and publishing windows. The lowest display order appears first.</p></div>
        <a href="{{ route('admin.hero-slides.create') }}" class="admin-action admin-action-primary">+ New slide</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        @forelse($slides as $slide)
            <article class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
                <div class="relative aspect-[16/7] overflow-hidden bg-stone-100">
                    <img src="{{ $slide->image_url }}" alt="" class="h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-5 text-white">
                        <div><p class="text-[10px] font-black uppercase tracking-widest text-amber-300">Order {{ $slide->sort_order }}</p><h3 class="mt-1 text-lg font-black leading-tight">{{ $slide->title }}</h3></div>
                        @php($statusClasses = match($slide->status) {'Live' => 'bg-emerald-100 text-emerald-800', 'Scheduled' => 'bg-amber-100 text-amber-800', 'Expired' => 'bg-stone-200 text-stone-700', default => 'bg-red-100 text-red-800'})
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-black {{ $statusClasses }}">{{ $slide->status }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                    <p class="text-xs text-stone-500">@if($slide->starts_at)Starts {{ $slide->starts_at->format('d M Y, g:i A') }}@else Always available @endif @if($slide->ends_at) · Ends {{ $slide->ends_at->format('d M Y, g:i A') }}@endif</p>
                    <div class="flex gap-2"><a href="{{ route('admin.hero-slides.edit', $slide) }}" class="admin-action admin-action-secondary admin-action-sm">Edit</a><form method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}" data-confirm="Delete this homepage slide permanently?">@csrf @method('DELETE')<button class="admin-action admin-action-danger admin-action-sm">Delete</button></form></div>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center lg:col-span-2"><h3 class="text-lg font-black">No homepage slides yet</h3><p class="mt-2 text-sm text-stone-500">Create a slide to publish your first homepage campaign.</p><a href="{{ route('admin.hero-slides.create') }}" class="admin-action admin-action-primary mt-5">Create a slide</a></div>
        @endforelse
    </div>
</div>
@endsection
