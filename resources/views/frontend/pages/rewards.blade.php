@extends('layouts.app')

@section('title', 'Rewards and referrals · Earn points with Pahadi Roots')
@section('meta_description', 'Learn how Pahadi Roots loyalty points and referral rewards work, how to invite friends, earn points and redeem them at checkout.')

@push('head')
<x-structured-data :data="[
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How do I refer a friend?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Sign in, open My Account and share your personal referral link. Your friend must register through that link in the same browser session.']],
        ['@type' => 'Question', 'name' => 'When are referral points added?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Points are added after the referred customer’s first qualifying paid order, or when their cash-on-delivery order is delivered.']],
        ['@type' => 'Question', 'name' => 'How do I spend loyalty points?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Enter the number of points you want to use at checkout. The available discount and redemption limit are shown before you place the order.']],
    ],
]" />
@endpush

@section('content')
@php
    $referrerPoints = (int) config('commerce.loyalty.referrer_reward');
    $friendPoints = (int) config('commerce.loyalty.referred_customer_reward');
    $pointValue = (float) config('commerce.loyalty.rupees_per_point');
    $maximumRedemption = (int) config('commerce.loyalty.maximum_redemption_percent');
    $purchaseRate = (float) config('commerce.loyalty.points_per_rupee');
@endphp

<x-page-hero eyebrow="Pahadi Roots rewards" title="Good things are better when shared." description="Earn points as you shop, invite friends to discover the hills, and use your balance for savings at checkout. No subscription and no joining fee." :image="asset('assets/img/editorial/rolling-cultivated-hills.webp')" image-position="center 54%">
    <x-slot:actions>@auth<a href="{{ route('profile') }}" class="button-primary !bg-white !text-brand-950 hover:!bg-brand-50">View my rewards</a>@else<a href="{{ route('register') }}" class="button-primary !bg-white !text-brand-950 hover:!bg-brand-50">Create a free account</a>@endauth<a href="#how-it-works" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-white/30 px-5 py-3 text-sm font-black text-white hover:bg-white/10">How it works</a></x-slot:actions>
</x-page-hero>

<section class="-mt-6 relative z-10 mx-4 grid overflow-hidden rounded-xl border border-stone-200 bg-white shadow-lg sm:mx-8 md:grid-cols-3" aria-label="Rewards at a glance">
    <div class="p-6 sm:p-7"><p class="text-3xl font-black text-brand-800">{{ $referrerPoints }}</p><p class="mt-1 text-sm font-black text-stone-900">points for you</p><p class="mt-2 text-xs leading-5 text-stone-500">After your friend’s first qualifying order.</p></div>
    <div class="border-y border-stone-200 p-6 sm:p-7 md:border-x md:border-y-0"><p class="text-3xl font-black text-saffron">{{ $friendPoints }}</p><p class="mt-1 text-sm font-black text-stone-900">points for your friend</p><p class="mt-2 text-xs leading-5 text-stone-500">A welcome reward after their first qualifying order.</p></div>
    <div class="p-6 sm:p-7"><p class="text-3xl font-black text-brand-800">₹{{ number_format($pointValue, 2) }}</p><p class="mt-1 text-sm font-black text-stone-900">value of each point</p><p class="mt-2 text-xs leading-5 text-stone-500">Redeem up to {{ $maximumRedemption }}% of eligible merchandise value.</p></div>
</section>

<section id="how-it-works" class="section-space scroll-mt-28">
    <div class="max-w-2xl"><p class="eyebrow">Invite and earn</p><h2 class="section-title mt-3">Your referral journey</h2><p class="mt-4 text-sm leading-7 text-stone-600">The referral is recorded through your personal link, so your friend does not need to remember or type a code at checkout.</p></div>
    <ol class="mt-8 grid gap-4 md:grid-cols-3">
        @foreach([
            ['01', 'Copy your personal link', 'Sign in and open My Account. Your unique referral link is ready to copy and share.'],
            ['02', 'Your friend creates an account', 'They open the link and register in that same browser session. This connects the new account to you.'],
            ['03', 'Both balances are rewarded', 'After their first qualifying order is paid—or a COD order is delivered—the points appear automatically.'],
        ] as [$number, $title, $description])
        <li class="relative overflow-hidden rounded-xl border border-stone-200 bg-white p-6"><span class="text-xs font-black tracking-widest text-saffron">{{ $number }}</span><h3 class="mt-8 text-lg font-black text-brand-950">{{ $title }}</h3><p class="mt-3 text-sm leading-7 text-stone-600">{{ $description }}</p></li>
        @endforeach
    </ol>
    @auth
        <div class="mt-6 rounded-xl border border-brand-200 bg-brand-50 p-5 sm:p-6"><div class="flex flex-col gap-4 lg:flex-row lg:items-end"><div class="min-w-0 flex-1"><label for="rewardsReferralLink" class="field-label">Your referral link</label><input id="rewardsReferralLink" readonly value="{{ route('refer', auth()->user()->referral_code) }}" class="field-control bg-white"></div><button type="button" class="button-primary shrink-0" data-copy-referral data-copy-target="rewardsReferralLink">Copy referral link</button></div><p class="mt-3 text-xs text-brand-800" data-copy-feedback aria-live="polite">Share it by WhatsApp, email or any messaging app.</p></div>
    @else
        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-brand-200 bg-brand-50 p-6"><div><h3 class="font-black text-brand-950">Ready to invite someone?</h3><p class="mt-1 text-sm text-brand-800">Create an account to receive your personal referral link.</p></div><a href="{{ route('register') }}" class="button-primary">Join Pahadi Roots</a></div>
    @endauth
</section>

<section class="grid overflow-hidden rounded-xl border border-stone-200 bg-white lg:grid-cols-2">
    <div class="bg-[#f4eee2] p-7 sm:p-10 lg:p-12"><p class="eyebrow">Earn as you shop</p><h2 class="font-display mt-4 text-4xl font-semibold leading-tight text-brand-950">Turn everyday orders into future savings.</h2><p class="mt-5 text-sm leading-7 text-stone-600">Eligible purchases earn {{ rtrim(rtrim(number_format($purchaseRate, 2), '0'), '.') }} point for every ₹1 spent. Your confirmed balance and complete activity history are available in My Account.</p><a href="{{ route('products.index') }}" class="button-primary mt-7">Shop mountain produce</a></div>
    <div class="p-7 sm:p-10 lg:p-12"><p class="eyebrow">Use your balance</p><h2 class="font-display mt-4 text-4xl font-semibold leading-tight text-brand-950">Redeem clearly at checkout.</h2><ol class="mt-7 space-y-5">@foreach(['Sign in and add products or bundles to your cart.', 'Continue to checkout and enter the number of points to use.', 'Review the points discount and final total before placing your order.'] as $index => $step)<li class="flex gap-4"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-black text-brand-800">{{ $index + 1 }}</span><p class="pt-1 text-sm leading-6 text-stone-600">{{ $step }}</p></li>@endforeach</ol><p class="mt-6 rounded-lg bg-amber-50 p-4 text-xs leading-6 text-amber-900"><strong>Good to know:</strong> rewards are not cash, cannot be transferred, and may be reversed if the qualifying order is cancelled or refunded.</p></div>
</section>

<section class="section-space pb-0">
    <div class="text-center"><p class="eyebrow">Common questions</p><h2 class="section-title mt-3">Rewards made clear</h2></div>
    <div class="mx-auto mt-8 max-w-3xl divide-y divide-stone-200 overflow-hidden rounded-xl border border-stone-200 bg-white">
        @foreach([
            ['Where is my referral link?', 'Sign in and open My Account. The referral and loyalty card contains your personal link, signup totals, qualified referrals and points activity.'],
            ['Can an existing customer use my referral link?', 'Referral rewards are for a new customer who registers after opening your link. A referral cannot be attached to an account that already exists.'],
            ['When does an order qualify?', 'Online-payment orders qualify after successful payment. Cash-on-delivery orders qualify when the order is marked delivered. Only the friend’s first qualifying order earns the referral rewards.'],
            ['What happens after a cancellation or refund?', 'Points awarded from that order are reversed automatically so balances and reports remain accurate.'],
            ['Can I combine points and a coupon?', 'Checkout calculates each eligible benefit and shows the complete order summary before you place the order. Coupon and points limits still apply.'],
        ] as [$question, $answer])
        <details class="group p-5 sm:p-6"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-black text-brand-950"><span>{{ $question }}</span><span class="text-xl text-brand-600 transition group-open:rotate-45" aria-hidden="true">+</span></summary><p class="mt-4 max-w-2xl text-sm leading-7 text-stone-600">{{ $answer }}</p></details>
        @endforeach
    </div>
</section>
@endsection

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">document.querySelector('[data-copy-referral]')?.addEventListener('click', async event => { const input = document.getElementById(event.currentTarget.dataset.copyTarget); const feedback = document.querySelector('[data-copy-feedback]'); try { await navigator.clipboard.writeText(input.value); event.currentTarget.textContent = 'Copied'; if (feedback) feedback.textContent = 'Referral link copied. It is ready to share.'; } catch (_) { input.select(); document.execCommand('copy'); event.currentTarget.textContent = 'Copied'; } });</script>
@endpush
