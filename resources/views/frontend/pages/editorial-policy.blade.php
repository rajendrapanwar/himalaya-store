@extends('layouts.app')
@section('title', 'Editorial policy · Pahadi Roots')
@section('meta_description', 'Learn how Pahadi Roots researches, reviews, dates and corrects product guides and sourcing content.')
@section('canonical', route('editorial.policy'))
@section('content')
<nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-buransh">Home</a><span>/</span><span class="text-stone-800">Editorial policy</span></nav>
<x-page-hero compact eyebrow="Trust and accuracy" title="How we research and maintain our content." description="Our guides are written to help customers understand products—not to manufacture expertise or replace qualified professional advice." :image="asset('assets/img/editorial/tea-farmer-hillside.webp')" image-position="center 52%" />
<div class="mx-auto mt-8 max-w-4xl space-y-4">
    @foreach([
        ['Who writes our guides','Content is prepared by the Pahadi Roots editorial team using product records, supplier information where available, practical preparation guidance and reputable public sources. We do not invent certifications, producer histories or health claims.'],
        ['Product and commercial independence','Editorial explanations are kept distinct from customer reviews and promotional messages. A product being sold by Pahadi Roots does not change the standard used to describe its ingredients, care or limitations.'],
        ['Review and update process','Every guide displays publication and update dates. We review an article when related product information, store policy or reliable guidance changes materially.'],
        ['Corrections','If you identify an error, email support@pahadiroots.in with the page link and supporting detail. Material corrections are made promptly and the article update date is revised.'],
        ['Health and safety boundaries','Our journal provides general food and product-use information. It is not medical, nutritional or legal advice. Safety-sensitive claims require authoritative support and are written conservatively.'],
    ] as [$heading,$copy])<section class="surface-card p-6 sm:p-8"><h2 class="font-display text-2xl font-semibold">{{ $heading }}</h2><p class="mt-4 leading-8 text-stone-600">{{ $copy }}</p></section>@endforeach
</div>
@endsection
