@props(['id', 'name' => 'password', 'autocomplete' => 'current-password', 'invalid' => false])
<div class="relative">
    <input id="{{ $id }}" name="{{ $name }}" type="password" autocomplete="{{ $autocomplete }}" {{ $attributes->class(['field-control pr-14']) }} aria-invalid="{{ $invalid ? 'true' : 'false' }}">
    <button type="button" class="absolute right-2 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full text-stone-500 transition hover:bg-stone-100 hover:text-brand-900" data-password-toggle aria-controls="{{ $id }}" aria-pressed="false" aria-label="Show password">
        <svg class="h-5 w-5" data-icon-show fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
        <svg class="hidden h-5 w-5" data-icon-hide fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7a9.7 9.7 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
    </button>
</div>
