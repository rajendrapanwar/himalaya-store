@extends('layouts.app')
@section('title', 'Choose a new password · Pahadi Roots')
@section('content')
<x-auth-shell eyebrow="Account security" title="A fresh start for your account." description="Choose a strong password you have not used elsewhere. Other signed-in devices will be signed out." image="assets/img/editorial/darjeeling-tea-garden.webp">
    <h1 class="font-display text-4xl font-medium text-ink">Create a new password</h1>
    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">@csrf<input type="hidden" name="token" value="{{ $token }}">
        <div><label for="resetEmail" class="field-label">Email address</label><input id="resetEmail" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required class="field-control"></div>
        <div><label for="resetPassword" class="field-label">New password</label><x-password-input id="resetPassword" autocomplete="new-password" required /><p class="field-help">Use at least 10 characters with uppercase, lowercase and a number.</p></div>
        <div><label for="resetConfirm" class="field-label">Confirm password</label><x-password-input id="resetConfirm" name="password_confirmation" autocomplete="new-password" required /></div>
        @if($errors->any())<div role="alert" class="rounded-2xl bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>@endif
        <button class="button-primary w-full" type="submit">Reset password <span class="button-arrow" aria-hidden="true">→</span></button>
    </form>
</x-auth-shell>
@endsection
