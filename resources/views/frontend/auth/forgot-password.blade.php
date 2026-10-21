@extends('layouts.app')
@section('title', 'Reset your password · Pahadi Roots')
@section('content')
<x-auth-shell eyebrow="Account recovery" title="It happens to all of us." description="We will email a secure link so you can choose a new password." image="assets/img/editorial/rolling-cultivated-hills.webp">
    <p class="eyebrow eyebrow-line md:hidden">Account recovery</p>
    <h1 class="font-display mt-4 text-4xl font-medium text-ink md:mt-0">Forgot your password?</h1>
    <p class="mt-3 text-sm leading-6 text-stone-600">Enter your email and we will send a secure reset link.</p>
    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">@csrf
        <div><label for="forgotEmail" class="field-label">Email address</label><input id="forgotEmail" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="field-control">@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
        <button class="button-primary w-full" type="submit">Send reset link <span class="button-arrow" aria-hidden="true">→</span></button>
    </form>
    <p class="mt-7 text-center text-sm text-stone-600">Remembered it? <a href="{{ route('login') }}" class="font-bold text-buransh hover:underline">Back to sign in</a></p>
</x-auth-shell>
@endsection
