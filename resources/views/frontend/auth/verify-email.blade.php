@extends('layouts.app')
@section('title', 'Verify your email · Pahadi Roots')
@section('content')
<div class="mx-auto max-w-xl py-6" data-reveal="scale">
    <div class="relative overflow-hidden rounded-[2rem] bg-white p-10 text-center shadow-[var(--shadow-lg)] ring-1 ring-stone-200/70">
        <svg class="pointer-events-none absolute inset-x-0 bottom-0 h-24 w-full text-brand-50" viewBox="0 0 600 100" preserveAspectRatio="none" fill="currentColor" aria-hidden="true"><path d="M0 100 90 40l60 30 110-60 90 50 80-30 170 70Z"/></svg>
        <div class="relative">
            <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-sun-soft text-buransh"><svg class="h-9 w-9 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 6h16v12H4V6Zm0 0 8 7 8-7"/></svg></span>
            <h1 class="font-display mt-6 text-4xl font-medium text-ink">Verify your email</h1>
            <p class="mt-3 leading-7 text-stone-600">Use the link sent to <strong class="text-ink">{{ auth()->user()->email }}</strong>. Email verification is required before checkout.</p>
            <form method="POST" action="{{ route('verification.send') }}" class="mt-8">@csrf<button class="button-primary" type="submit">Resend verification email</button></form>
            <p class="mt-5 text-xs text-stone-500">Can’t find it? Check your spam or promotions folder.</p>
        </div>
    </div>
</div>
@endsection
