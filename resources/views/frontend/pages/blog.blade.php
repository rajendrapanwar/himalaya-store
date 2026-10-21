@extends('layouts.app')
@php($journalPage = $posts->currentPage())
@section('title', 'Himalayan Food & Culture Journal'.($journalPage > 1 ? ' | Page '.$journalPage : '').' | Pahadi Roots')
@section('meta_description', 'Stories and practical guides to Himalayan ingredients, Uttarakhand food traditions, sourcing, product care and mindful mountain living.'.($journalPage > 1 ? ' Page '.$journalPage.'.' : ''))
@section('canonical', route('blog', request()->filled('topic') ? [] : array_filter(['page' => $journalPage > 1 ? $journalPage : null])))
@if(request()->filled('topic')) @section('robots', 'noindex, follow') @endif
@section('pagination_links')
    @if(! request()->filled('topic') && $posts->previousPageUrl())<link rel="prev" href="{{ route('blog', array_filter(['page' => $journalPage - 1 > 1 ? $journalPage - 1 : null])) }}">@endif
    @if(! request()->filled('topic') && $posts->nextPageUrl())<link rel="next" href="{{ route('blog', ['page' => $journalPage + 1]) }}">@endif
@endsection
@push('head')
<x-structured-data :data="[
    '@context' => 'https://schema.org', '@type' => 'CollectionPage',
    'name' => 'The Pahadi Journal', 'url' => route('blog', request()->filled('topic') ? [] : array_filter(['page' => $journalPage > 1 ? $journalPage : null])),
    'description' => 'Stories and practical guides to Himalayan ingredients, sourcing and mountain food traditions.',
    'primaryImageOfPage' => $posts->first()?->image_url ? ['@type' => 'ImageObject', 'url' => $posts->first()->image_url] : null,
    'mainEntity' => ['@type' => 'ItemList', 'itemListElement' => $posts->getCollection()->map(fn ($post, $index) => ['@type' => 'ListItem', 'position' => $posts->firstItem() + $index, 'url' => route('blog.show', $post->slug), 'name' => $post->title])->all()],
]" />
@endpush
@section('content')
<nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-buransh">Home</a><span aria-hidden="true">/</span><span class="text-stone-800">Journal</span></nav>

<header class="overflow-hidden rounded-xl border border-stone-200 bg-white">
    <div class="grid lg:grid-cols-[.9fr_1.1fr] lg:items-center">
        <div class="px-7 py-11 sm:px-11 sm:py-14"><p class="eyebrow">The Pahadi Journal</p><h1 class="display-title mt-4 text-ink">Stories with roots in the mountains.</h1><p class="mt-5 max-w-xl text-base leading-8 text-stone-600">Practical guides, ingredient stories and thoughtful notes from Uttarakhand and the wider Himalayan region.</p><a href="#latest-stories" class="button-primary mt-7">Explore the journal <span aria-hidden="true">↓</span></a></div>
        @if($posts->first()?->image_url)<div class="relative min-h-[300px] overflow-hidden bg-stone-100 lg:min-h-[470px]"><img src="{{ $posts->first()->image_url }}" alt="{{ $posts->first()->image_alt }}" class="absolute inset-0 h-full w-full object-cover" width="1000" height="760" fetchpriority="high"><div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-night/85 via-night/35 to-transparent p-7 pt-24 text-white sm:p-9"><p class="text-[10px] font-black uppercase tracking-[.16em] text-amber-300">Featured reading</p><a href="{{ route('blog.show', $posts->first()->slug) }}" class="font-display mt-2 block max-w-xl text-2xl font-semibold leading-tight hover:text-amber-100 sm:text-3xl">{{ $posts->first()->title }}</a></div></div>@endif
    </div>
</header>

@if($topics->isNotEmpty())<nav class="mt-7 flex gap-2 overflow-x-auto pb-2" aria-label="Journal topics"><a href="{{ route('blog') }}" class="whitespace-nowrap rounded-full border px-4 py-2 text-xs font-black {{ request('topic') ? 'border-stone-200 bg-white text-stone-700' : 'border-brand-800 bg-brand-800 text-white' }}">All stories</a>@foreach($topics as $topic)<a href="{{ route('blog', ['topic' => $topic]) }}" class="whitespace-nowrap rounded-full border px-4 py-2 text-xs font-black {{ request('topic') === $topic ? 'border-brand-800 bg-brand-800 text-white' : 'border-stone-200 bg-white text-stone-700 hover:border-brand-300' }}">{{ $topic }}</a>@endforeach</nav>@endif

<section id="latest-stories" class="section-space pb-0">
    <div class="flex items-end justify-between gap-4"><div><p class="eyebrow">Ideas, origins and everyday use</p><h2 class="section-title mt-3">{{ request('topic') ? request('topic').' stories' : 'Latest from the journal' }}</h2></div><p class="hidden text-xs font-semibold text-stone-500 sm:block">{{ $posts->total() }} {{ Str::plural('article', $posts->total()) }}</p></div>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($posts as $post)
            <article class="journal-card group overflow-hidden rounded-xl border border-stone-200 bg-white">
                <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-[16/10] w-full overflow-hidden bg-stone-100">@if($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $post->image_alt }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.025]" loading="lazy" decoding="async" width="720" height="450">@endif</a>
                <div class="flex min-h-[265px] flex-col p-6"><div class="flex items-center justify-between gap-3 text-[10px] font-black uppercase tracking-[.13em] text-buransh"><span>{{ $post->category ?: 'Journal' }}</span><span class="text-stone-400">{{ $post->reading_time }} min read</span></div><h3 class="font-display mt-4 text-2xl font-semibold leading-tight text-ink"><a href="{{ route('blog.show', $post->slug) }}" class="hover:text-buransh">{{ $post->title }}</a></h3><p class="mt-3 line-clamp-3 flex-1 text-sm leading-7 text-stone-600">{{ $post->excerpt }}</p><div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4"><span class="text-xs font-semibold text-stone-500">By {{ $post->author_name }}</span><a href="{{ route('blog.show', $post->slug) }}" class="text-sm font-black text-buransh">Read <span aria-hidden="true">→</span></a></div></div>
            </article>
        @empty<div class="surface-card col-span-full p-12 text-center"><h2 class="font-display text-2xl font-semibold">No stories found</h2><p class="mt-2 text-stone-500">Try another topic or return to all journal articles.</p></div>@endforelse
    </div>
    <div class="mt-8">{{ $posts->links() }}</div>
</section>

<aside class="mt-14 grid gap-6 rounded-xl border border-brand-200 bg-brand-50 p-7 sm:p-9 lg:grid-cols-[1fr_auto] lg:items-center"><div><p class="eyebrow">Our publishing standard</p><h2 class="font-display mt-3 text-3xl font-semibold text-ink">Useful, transparent and carefully framed.</h2><p class="mt-3 max-w-3xl text-sm leading-7 text-brand-900">Every story is dated, attributed and written to distinguish practical guidance from medical or exaggerated product claims.</p></div><a href="{{ route('editorial.policy') }}" class="button-secondary">Read our editorial policy</a></aside>
@endsection
