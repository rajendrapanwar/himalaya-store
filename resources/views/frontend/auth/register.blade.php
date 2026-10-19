@extends('layouts.app')
@section('title', 'Create an account · Pahadi Roots')
@section('content')
<x-auth-shell eyebrow="Join Pahadi Roots" title="More value from every mountain order." description="Create an account to earn loyalty points, use referral rewards, save wishlists and submit verified reviews." image="assets/img/editorial/uttarakhand-terrace-farming.webp" :points="['Loyalty points on eligible orders', 'Referral rewards for you and friends', 'Faster checkout with saved details']">
    <p class="eyebrow eyebrow-line md:hidden">Join Pahadi Roots</p>
    <h1 class="font-display mt-4 text-4xl font-medium text-ink md:mt-0">Create your account</h1>
    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-4">@csrf
        <div><label for="registerName" class="field-label">Full name</label><input id="registerName" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255" class="field-control" required autofocus>@error('name')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div><label for="registerEmail" class="field-label">Email address</label><input id="registerEmail" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" class="field-control" required>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div><label for="registerPassword" class="field-label">Password</label><x-password-input id="registerPassword" autocomplete="new-password" :invalid="$errors->has('password')" required /><p class="field-help">Use at least 10 characters with uppercase, lowercase and a number.</p>@error('password')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div><label for="confirmPassword" class="field-label">Confirm password</label><x-password-input id="confirmPassword" name="password_confirmation" autocomplete="new-password" required /></div>
        <button class="button-primary w-full" type="submit">Create account <span class="button-arrow" aria-hidden="true">→</span></button>
    </form>
    <p class="mt-7 text-center text-sm text-stone-600">Already registered? <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:underline">Sign in</a></p>
</x-auth-shell>
@endsection
