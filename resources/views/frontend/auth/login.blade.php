@extends('layouts.app')
@section('title', 'Sign in · Pahadi Roots')
@section('content')
<x-auth-shell eyebrow="Welcome back" title="Your mountain pantry, saved for you." description="Sign in to manage orders, wishlist favourites, loyalty points, referrals and verified reviews." :points="['Track every order to your door', 'Save products to your wishlist', 'Redeem loyalty rewards at checkout']">
    <p class="eyebrow eyebrow-line md:hidden">Welcome back</p>
    <h1 class="font-display text-4xl font-medium text-ink md:mt-0 mt-4">Sign in to your account</h1>
    <p class="mt-3 text-sm text-stone-500">Use the email address you registered with.</p>
    @include('partials.google-auth', ['label' => 'Continue with Google', 'note' => 'New here? This creates your account in one step.'])
    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">@csrf
        <div><label for="loginEmail" class="field-label">Email address</label><input id="loginEmail" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" class="field-control" required autofocus aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div><div class="flex items-center justify-between"><label for="loginPassword" class="field-label">Password</label><a href="{{ route('password.request') }}" class="mb-2 text-xs font-bold text-buransh hover:underline">Forgot password?</a></div><x-password-input id="loginPassword" required /></div>
        <label class="flex items-center gap-2.5 text-sm text-stone-600"><input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded"> Keep me signed in on this device</label>
        <button class="button-primary w-full" type="submit">Sign in <span class="button-arrow" aria-hidden="true">→</span></button>
    </form>
    <div class="my-7 flex items-center gap-4 text-xs font-bold uppercase tracking-[.16em] text-stone-400"><span class="h-px flex-1 bg-stone-200"></span>New here?<span class="h-px flex-1 bg-stone-200"></span></div>
    <a href="{{ route('register') }}" class="button-secondary w-full">Create a free account</a>
</x-auth-shell>
@endsection
