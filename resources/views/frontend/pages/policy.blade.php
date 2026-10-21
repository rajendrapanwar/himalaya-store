@extends('layouts.app')
@section('title', $title.' · Pahadi Roots')
@section('meta_description', match($kind) { 'shipping' => 'Read Pahadi Roots order processing, shipping charges, delivery estimates and address-change information.', 'returns' => 'Read the Pahadi Roots 30-day return, damaged-item, cancellation and refund process.', 'privacy' => 'Learn how Pahadi Roots collects, uses, protects and retains customer information.', default => 'Read the terms that apply when using the Pahadi Roots website and ordering our products.' })
@section('canonical', match($kind) { 'shipping' => route('shipping.policy'), 'returns' => route('returns.policy'), 'privacy' => route('privacy.policy'), default => route('terms') })
@push('head')<x-seo-breadcrumbs :items="[['name' => 'Home', 'url' => route('home')], ['name' => $title, 'url' => match($kind) { 'shipping' => route('shipping.policy'), 'returns' => route('returns.policy'), 'privacy' => route('privacy.policy'), default => route('terms') }]]" />@endpush
@section('content')
<nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-buransh">Home</a><span>/</span><span class="text-stone-800">{{ $title }}</span></nav>
@php
$sections = match($kind) {
 'shipping' => [
  ['Order processing','Orders are normally prepared within 1–2 business days after confirmation. Orders placed on Sundays or public holidays begin processing on the next business day.'],
  ['Delivery estimates','Estimated delivery depends on the destination pincode and courier network. Remote mountain and rural locations may require additional time. Estimates are indicative rather than guaranteed delivery dates.'],
  ['Shipping charges','Applicable shipping charges are calculated from the serviceable pincode and shown as part of the final order total. A free-shipping coupon is applied automatically when eligible.'],
  ['Packaging and tracking','Products are packed to protect them in transit. When tracking information is available, it appears in your order details. Courier systems may take time to activate a new tracking number.'],
  ['Delays and address changes','Weather, road closures, festivals or courier disruptions may cause delays. Contact us promptly for an address correction; changes cannot be guaranteed after dispatch.'],
 ],
 'returns' => [
  ['Return eligibility','Eligible unopened products may be requested for return within 30 days of delivery. Report an item that is damaged, incorrect, leaking or materially different from the order within 48 hours and include the order number, a description and clear photographs.'],
  ['Food and personal-use products','For safety and hygiene, opened food, wellness and personal-use goods cannot be returned because of preference or change of mind. This does not affect damaged or incorrect-item claims.'],
  ['Review and resolution','Our team reviews each request. Eligible cases may receive a replacement, store credit or refund to the original payment method. We may ask you to retain or return the product.'],
  ['Return shipping','Unless an item was damaged, incorrect or faulty when delivered, the customer is responsible for arranging and paying eligible return shipping. We will provide instructions before anything is sent back.'],
  ['Refund timing','Approved refunds are initiated promptly. Banks and payment providers can take 5–10 business days to complete processing. COD refunds may require verified bank details.'],
  ['Cancellations','An order may be cancelled before dispatch. Once shipped, it follows the return eligibility rules above.'],
 ],
 'privacy' => [
  ['Information we collect','We collect account details, contact information, delivery addresses, order activity, support messages, reviews, wishlist activity, referral information and basic technical data needed to operate the store.'],
  ['How we use information','We use information to provide accounts, fulfil orders, process payments, prevent misuse, respond to support, improve the store, administer loyalty and referrals, and meet legal obligations.'],
  ['Payments and service providers','Stripe processes online payment information; we do not store complete card details. We may share necessary details with couriers, hosting providers and service partners solely to provide the requested service.'],
  ['Cookies and sessions','Essential cookies keep you signed in, maintain your cart, protect forms and remember preferences. Disabling them may prevent important store functions from working.'],
  ['Your choices','You may request correction or deletion of eligible account data by contacting us. Some transaction records must be retained for tax, fraud-prevention or legal reasons.'],
  ['Security and retention','We apply reasonable safeguards and retain information only as long as needed for operations, legal requirements, dispute resolution and legitimate business records.'],
 ],
 default => [
  ['Using the website','You must provide accurate information, keep account credentials secure, use the store lawfully and avoid interfering with the website or another customer’s account.'],
  ['Products and availability','Product colours, natural variations, harvests, weights and packaging may differ slightly from photographs. Availability, pricing, promotions and descriptions may be corrected or updated.'],
  ['Orders and acceptance','Submitting an order is an offer to purchase. We may cancel or limit an order because of stock errors, pricing mistakes, suspected misuse, payment failure or delivery restrictions. Any collected amount will be handled appropriately.'],
  ['Payments','Available methods may include Stripe-supported cards and UPI as well as cash on delivery. You authorize the selected provider to process the order amount. Online payment status is confirmed through Stripe.'],
  ['Reviews and user content','Reviews must be genuine, relevant and respectful. We may moderate or remove spam, unlawful content, personal information or material unrelated to the product.'],
  ['Liability','To the extent permitted by law, we are not responsible for indirect losses or delays outside our reasonable control. Nothing in these terms removes rights that cannot legally be excluded.'],
  ['Changes and governing principles','We may update these terms as the store evolves. The version displayed when you use the site applies. These terms are intended to operate under applicable laws of India.'],
 ]
};
$policyHeroImage = match($kind) {
 'shipping' => asset('assets/img/editorial/uttarakhand-terrace-farming.webp'),
 'returns' => asset('assets/img/editorial/rolling-cultivated-hills.webp'),
 'privacy' => asset('assets/img/editorial/darjeeling-tea-garden.webp'),
 default => asset('assets/img/editorial/misty-tea-mountains.webp'),
};
@endphp
<x-page-hero compact eyebrow="Trust and transparency" :title="$title" description="This page explains what you can expect from Pahadi Roots and what we ask from customers." :image="$policyHeroImage" image-position="center 52%">
    <x-slot:meta>Last updated: 18 July 2026</x-slot:meta>
</x-page-hero>
<div class="mt-8 grid gap-8 lg:grid-cols-[260px_1fr]"><aside class="surface-card h-fit p-4 lg:sticky lg:top-32"><p class="eyebrow px-2">On this page</p><nav class="mt-3 space-y-1">@foreach($sections as $index=>$section)<a href="#section-{{ $index }}" class="block rounded-lg px-3 py-2 text-sm text-stone-600 hover:bg-brand-50 hover:text-buransh">{{ $section[0] }}</a>@endforeach</nav></aside><article class="space-y-4">@foreach($sections as $index=>$section)<section id="section-{{ $index }}" class="surface-card scroll-mt-32 p-6 sm:p-8"><h2 class="font-display text-2xl font-semibold">{{ $section[0] }}</h2><p class="mt-4 leading-8 text-stone-600">{{ $section[1] }}</p></section>@endforeach<section class="rounded-2xl bg-brand-100 p-6 sm:p-8"><h2 class="font-display text-2xl font-semibold text-ink">Need clarification?</h2><p class="mt-2 text-sm leading-7 text-brand-900">Email <a class="font-bold underline" href="mailto:support@pahadiroots.in">support@pahadiroots.in</a> or use our <a class="font-bold underline" href="{{ route('contact') }}">contact form</a>.</p></section></article></div>
@endsection
