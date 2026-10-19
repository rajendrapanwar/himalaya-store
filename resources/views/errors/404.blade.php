@extends('errors.layout')
@section('title', 'Page not found')
@section('code', '404')
@section('eyebrow', 'Lost on the trail')
@section('heading', 'This path doesn’t lead anywhere — yet.')
@section('message', 'The page may have moved, or the link may be mistyped. Let’s get you back to the mountain pantry.')
@section('actions')
    <a href="{{ url('/products') }}" class="button-primary">Explore the shop <span class="button-arrow" aria-hidden="true">→</span></a>
    <a href="{{ url('/') }}" class="button-secondary">Back to home</a>
    <form method="GET" action="{{ url('/products') }}" class="mt-2 flex w-full max-w-md gap-2 rounded-full bg-white p-1.5 ring-1 ring-stone-200" role="search"><label for="notFoundSearch" class="sr-only">Search products</label><input id="notFoundSearch" name="search" type="search" placeholder="Search honey, rajma, teas…" class="min-w-0 flex-1 !rounded-full !border-0 px-5 !shadow-none"><button class="button-primary !min-h-[44px]" type="submit">Search</button></form>
@endsection
