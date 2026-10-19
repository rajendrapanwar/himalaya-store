@extends('layouts.app')
@section('title', $post->seo_title ?: $post->title.' | Pahadi Roots Journal')
@section('meta_description', $post->meta_description ?: Str::limit($post->excerpt, 160, ''))
@section('canonical', route('blog.show', $post->slug))
@section('og_type', 'article')
@if($post->image_url) @section('og_image', $post->image_url) @section('og_image_alt', $post->image_alt ?: $post->title) @endif
@push('head')
<meta property="article:published_time" content="{{ $post->published_at->toAtomString() }}"><meta property="article:modified_time" content="{{ $post->updated_at->toAtomString() }}">@if($post->category)<meta property="article:section" content="{{ $post->category }}">@endif
<x-structured-data :data="['@context' => 'https://schema.org', '@graph' => [[
    '@type' => 'Article', '@id' => route('blog.show', $post->slug).'#article', 'headline' => $post->title,
    'description' => $post->meta_description ?: $post->excerpt, 'image' => $post->image_url,
    'datePublished' => $post->published_at->toAtomString(), 'dateModified' => $post->updated_at->toAtomString(),
    'mainEntityOfPage' => route('blog.show', $post->slug), 'author' => ['@type' => 'Person', 'name' => $post->author_name],
    'publisher' => ['@id' => route('home').'#organization'], 'articleSection' => $post->category, 'keywords' => $post->focus_keyword, 'wordCount' => str_word_count(strip_tags($post->content)), 'inLanguage' => config('seo.language'),
], ['@type' => 'BreadcrumbList', 'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Journal', 'item' => route('blog')],
    ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => route('blog.show', $post->slug)],
]]]]" />
@endpush
@section('content')
<article>
    <nav class="flex items-center gap-2 overflow-hidden text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-brand-700">Home</a><span>/</span><a href="{{ route('blog') }}" class="hover:text-brand-700">Journal</a><span>/</span><span class="truncate text-stone-800">{{ $post->title }}</span></nav>
    <header class="mx-auto mt-10 max-w-4xl text-center"><p class="eyebrow">{{ $post->category ?: 'The Pahadi Journal' }}</p><h1 class="display-title mt-5 text-brand-950">{{ $post->title }}</h1><p class="mx-auto mt-6 max-w-3xl text-lg leading-8 text-stone-600 sm:text-xl">{{ $post->excerpt }}</p><div class="mt-7 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-semibold text-stone-500"><span class="font-black text-brand-800">By {{ $post->author_name }}</span><span><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d M Y') }}</time></span><span>{{ $post->reading_time }} min read</span></div></header>
    @if($post->image_url)<figure class="mt-10 overflow-hidden rounded-xl border border-stone-200 bg-stone-100"><img src="{{ $post->image_url }}" alt="{{ $post->image_alt }}" class="aspect-[16/8] w-full object-cover" width="1240" height="620" fetchpriority="high">@if($post->image_alt)<figcaption class="border-t border-stone-200 bg-white px-5 py-3 text-center text-xs text-stone-500">{{ $post->image_alt }}</figcaption>@endif</figure>@endif

    <div class="journal-article mx-auto mt-10 max-w-3xl">
        @foreach(preg_split('/\R{2,}/', trim($post->content)) as $block)
            @php($block = trim($block))
            @if(Str::startsWith($block, '### '))<h3>{{ Str::after($block, '### ') }}</h3>
            @elseif(Str::startsWith($block, '## '))<h2>{{ Str::after($block, '## ') }}</h2>
            @elseif(Str::startsWith($block, '- '))<ul>@foreach(preg_split('/\R/', $block) as $item)<li>{{ Str::after(trim($item), '- ') }}</li>@endforeach</ul>
            @else<p>{{ $block }}</p>@endif
        @endforeach
    </div>
    <footer class="mx-auto mt-10 max-w-3xl border-t border-stone-200 pt-6 text-sm leading-7 text-stone-600">This article provides general food and product-use information, not medical advice. Learn more in our <a href="{{ route('editorial.policy') }}" class="font-bold text-brand-700 underline">editorial policy</a>.</footer>
</article>

@if($relatedPosts->isNotEmpty())<section class="section-space pb-0"><div class="flex items-end justify-between gap-4"><div><p class="eyebrow">Continue reading</p><h2 class="section-title mt-3">More from the journal</h2></div><a href="{{ route('blog') }}" class="button-quiet">View all stories →</a></div><div class="mt-7 grid gap-5 sm:grid-cols-3">@foreach($relatedPosts as $related)<article class="journal-card overflow-hidden rounded-xl border border-stone-200 bg-white">@if($related->image_url)<a href="{{ route('blog.show', $related->slug) }}"><img src="{{ $related->image_url }}" alt="{{ $related->image_alt }}" class="aspect-[16/10] w-full object-cover" loading="lazy" width="500" height="310"></a>@endif<div class="p-5"><p class="text-[10px] font-black uppercase tracking-wider text-brand-700">{{ $related->category }}</p><h3 class="font-display mt-3 text-xl font-semibold leading-tight"><a href="{{ route('blog.show', $related->slug) }}" class="hover:text-brand-700">{{ $related->title }}</a></h3><p class="mt-3 text-xs text-stone-500">{{ $related->reading_time }} min read</p></div></article>@endforeach</div></section>@endif
@endsection
