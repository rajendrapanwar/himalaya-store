@extends('layouts.app')
@section('title', 'Bulk, wedding and corporate gifting · Pahadi Roots')
@section('meta_description', 'Order Himalayan gift boxes and pantry products in bulk for Diwali, weddings, corporate gifting and events. Tell us the quantity and date and get a quote within one business day.')
@section('canonical', route('bulk-orders'))
@push('head')<x-seo-breadcrumbs :items="[['name' => 'Home', 'url' => route('home')], ['name' => 'Bulk and corporate orders', 'url' => route('bulk-orders')]]" />@endpush
@section('content')
<nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-moss">Home</a><span>/</span><span class="text-stone-800">Bulk and corporate orders</span></nav>
<x-page-hero compact eyebrow="Bulk and corporate orders" title="Gifts from the hills, for ten people or a thousand." description="Festive hampers, wedding favours, employee and client gifts. Tell us what you need and we will send a quote within one business day." :image="asset('assets/img/editorial/uttarakhand-terrace-farming.webp')" image-position="center 60%" />

<div class="mt-8 grid gap-6 lg:grid-cols-[.72fr_1.28fr]">
    <div class="space-y-6">
        <section class="surface-card p-6">
            <h2 class="font-display text-2xl font-semibold">How it works</h2>
            <ol class="mt-6 space-y-5 text-sm leading-7 text-stone-600">
                <li class="flex gap-4"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-moss text-xs font-black text-white">1</span><span><strong class="block text-stone-900">Tell us what you need</strong>Quantity, budget per gift and the date you need them by.</span></li>
                <li class="flex gap-4"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-moss text-xs font-black text-white">2</span><span><strong class="block text-stone-900">Get a quote</strong>We suggest boxes that fit your budget, with bulk pricing.</span></li>
                <li class="flex gap-4"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-moss text-xs font-black text-white">3</span><span><strong class="block text-stone-900">We pack and deliver</strong>To one address or to each person on your list.</span></li>
            </ol>
        </section>
        <section class="surface-card p-6">
            <h2 class="font-display text-2xl font-semibold">Good to know</h2>
            <ul class="mt-5 space-y-3 text-sm leading-7 text-stone-600">
                <li>Bulk pricing starts at 10 boxes.</li>
                <li>Add your logo card or a personal note to every box.</li>
                <li>GST invoice available for businesses.</li>
                <li>For festive seasons, please order at least two weeks ahead.</li>
            </ul>
            <a href="https://wa.me/{{ config('commerce.whatsapp_number') }}?text={{ urlencode('Hello Pahadi Roots, I would like a quote for a bulk order.') }}" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#1a7f43] px-5 py-3 text-sm font-bold text-white hover:bg-[#156a38]">Prefer WhatsApp? Chat with us</a>
        </section>
    </div>

    <form method="POST" action="{{ route('bulk-orders.send') }}" class="surface-card p-6 sm:p-8">@csrf
        <input name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
        <h2 class="font-display text-3xl font-semibold">Request a quote</h2>
        <p class="mt-2 text-sm text-stone-500">We normally reply within one business day.</p>
        <div class="mt-7 grid gap-5 sm:grid-cols-2">
            <div><label for="bulkName" class="field-label">Full name</label><input id="bulkName" name="name" value="{{ old('name', auth()->user()?->name) }}" autocomplete="name" maxlength="120" class="field-control" required>@error('name')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="bulkCompany" class="field-label">Company or event <span class="font-normal text-stone-500">(optional)</span></label><input id="bulkCompany" name="company" value="{{ old('company') }}" autocomplete="organization" maxlength="120" class="field-control">@error('company')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="bulkEmail" class="field-label">Email</label><input id="bulkEmail" name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" maxlength="255" class="field-control" required>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="bulkPhone" class="field-label">Phone or WhatsApp</label><input id="bulkPhone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" maxlength="20" class="field-control" required>@error('phone')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="bulkQuantity" class="field-label">How many gifts?</label><input id="bulkQuantity" name="quantity" type="number" min="10" max="100000" value="{{ old('quantity') }}" inputmode="numeric" class="field-control" placeholder="For example 50" required>@error('quantity')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="bulkBudget" class="field-label">Budget per gift (₹) <span class="font-normal text-stone-500">(optional)</span></label><input id="bulkBudget" name="budget" type="number" min="100" max="100000" value="{{ old('budget') }}" inputmode="numeric" class="field-control" placeholder="For example 800">@error('budget')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div class="sm:col-span-2"><label for="bulkNeededBy" class="field-label">Needed by <span class="font-normal text-stone-500">(optional)</span></label><input id="bulkNeededBy" name="needed_by" type="date" min="{{ now()->addDay()->toDateString() }}" value="{{ old('needed_by') }}" class="field-control sm:max-w-xs">@error('needed_by')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div class="sm:col-span-2"><label for="bulkMessage" class="field-label">Anything else we should know? <span class="font-normal text-stone-500">(optional)</span></label><textarea id="bulkMessage" name="message" rows="5" maxlength="2000" class="field-control" placeholder="Occasion, products you like, delivery cities, branding…">{{ old('message') }}</textarea>@error('message')<p class="field-error">{{ $message }}</p>@enderror</div>
        </div>
        <button class="button-primary mt-6" type="submit">Request my quote</button>
    </form>
</div>
@if(session('bulk_enquiry_sent'))<x-analytics-event name="generate_lead" :params="['lead_source' => 'bulk_order']" />@endif
@endsection
