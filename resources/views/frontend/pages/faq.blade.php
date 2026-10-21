@extends('layouts.app')
@section('title', 'Frequently asked questions · Pahadi Roots')
@section('meta_description', 'Answers about Pahadi Roots products, delivery, payments, coupons, loyalty rewards, reviews, returns and customer support.')
@section('canonical', route('faq'))
@php($faqs = [
['Where do your products come from?','We focus on products connected to Uttarakhand and the wider Himalayan region. Origin, producer, ingredients and batch details are shown whenever the information is available.'],
['How can I check delivery to my area?','Enter your six-digit Indian pincode on a product page. Final delivery availability, timing, charges and COD eligibility are confirmed during checkout.'],
['How long will my order take?','Orders are generally prepared within 1–2 business days. Delivery time varies by pincode, courier coverage, weather and road conditions.'],
['Which payment methods are supported?','Stripe checkout supports eligible UPI options, debit cards and credit cards. Cash on delivery is available for supported pincodes.'],
['Can I apply a coupon?','Enter one active coupon code in the cart. Its discount and updated total appear before checkout when the cart meets the coupon conditions.'],
['How do loyalty points and referrals work?','Signed-in customers earn points from eligible purchases and can share their referral code. Balances and referral information are available in the account area.'],
['Can I change or cancel an order?','Contact customer care as soon as possible. We can normally cancel or correct an order before dispatch, but changes cannot be guaranteed once shipping begins.'],
['What if an item arrives damaged or incorrect?','Submit a return request within 48 hours with your order number and clear photographs. We will review the issue and arrange an eligible replacement, credit or refund.'],
['Are natural variations normal?','Yes. Colour, texture, aroma and flavour can vary between harvests and batches. Honey crystallisation is also a natural process.'],
['How can I contact you?','Use the contact form, email support@pahadiroots.in or open the WhatsApp button. Customer care operates Monday–Saturday, 10:00 AM–6:00 PM IST.'],
])
@push('head')<x-seo-breadcrumbs :items="[['name' => 'Home', 'url' => route('home')], ['name' => 'Frequently asked questions', 'url' => route('faq')]]" /><x-structured-data :data="['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => collect($faqs)->map(fn ($faq) => ['@type' => 'Question', 'name' => $faq[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]]])->all()]" />@endpush
@section('content')
<nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb"><a href="{{ route('home') }}" class="hover:text-buransh">Home</a><span>/</span><span class="text-stone-800">FAQs</span></nav>
<x-page-hero compact eyebrow="Help centre" title="Frequently asked questions" description="Quick answers about products, delivery, payments, coupons, rewards and returns." :image="asset('assets/img/editorial/misty-tea-mountains.webp')" image-position="center 55%" />
<div class="mx-auto mt-8 max-w-4xl space-y-3">@foreach($faqs as $faq)<details class="group surface-card p-5 sm:p-6"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-extrabold">{{ $faq[0] }}<span class="text-2xl font-normal text-buransh transition group-open:rotate-45" aria-hidden="true">+</span></summary><p class="mt-4 border-t border-stone-100 pt-4 text-sm leading-7 text-stone-600">{{ $faq[1] }}</p></details>@endforeach</div>
<section class="mx-auto mt-8 max-w-4xl rounded-2xl bg-brand-100 p-8 text-center"><h2 class="font-display text-2xl font-semibold text-ink">Still need help?</h2><p class="mt-2 text-sm text-brand-900">Send us a message and our team will normally reply within one business day.</p><a href="{{ route('contact') }}" class="button-primary mt-5">Contact us</a></section>
@endsection
