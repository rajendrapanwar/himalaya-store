@extends('layouts.app')

@section('title', 'My account · Pahadi Roots')

@section('content')
@php
    $firstName = Str::before($user->name, ' ');
    $orderProgress = $latestOrder ? match($latestOrder->order_status) { 'processing' => 45, 'shipped' => 72, 'delivered' => 100, 'cancelled' => 100, default => 18 } : 0;
@endphp
<div class="mx-auto max-w-7xl py-8 sm:py-12">
    <section class="relative overflow-hidden rounded-[2rem] bg-brand-950 px-6 py-8 text-white shadow-xl shadow-brand-950/10 sm:px-9 sm:py-10">
        <div class="absolute -right-12 -top-20 h-72 w-72 rounded-full border-[48px] border-white/[.04]" aria-hidden="true"></div>
        <div class="relative flex flex-col gap-7 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-5">
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-full border-4 border-white/20 bg-brand-700 shadow-lg">
                    @if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">@else<div class="grid h-full w-full place-items-center text-2xl font-black">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</div>@endif
                </div>
                <div><p class="text-xs font-black uppercase tracking-[.18em] text-amber-300">Your mountain pantry</p><h1 class="font-display mt-2 text-4xl font-semibold sm:text-5xl">Welcome back, {{ $firstName }}.</h1><p class="mt-2 text-sm text-brand-100">Everything you have ordered, saved and earned—together in one place.</p></div>
            </div>
            <a href="{{ route('products.index') }}" class="button-secondary shrink-0">Continue shopping</a>
        </div>
    </section>

    @if(!$user->hasVerifiedEmail())
        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900"><span><b>Verify your email to check out.</b> We will send a fresh secure link.</span><form method="POST" action="{{ route('verification.send') }}">@csrf<button class="rounded-xl bg-amber-700 px-4 py-2 font-bold text-white">Resend verification</button></form></div>
    @endif

    <nav class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4" aria-label="Account shortcuts">
        <a href="{{ route('orders') }}" class="surface-card group p-4 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md"><span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14l-1 12H6L5 8Zm3 0a4 4 0 0 1 8 0"/></svg></span><strong class="mt-3 block">My orders</strong><span class="text-xs text-stone-500">{{ $dashboardStats['active_orders'] }} active</span></a>
        <a href="{{ route('wishlist.index') }}" class="surface-card group p-4 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md"><span class="grid h-10 w-10 place-items-center rounded-xl bg-rose-50 text-rose-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></svg></span><strong class="mt-3 block">Wishlist</strong><span class="text-xs text-stone-500">{{ $dashboardStats['wishlist'] }} saved</span></a>
        <a href="{{ route('notifications.index') }}" class="surface-card group p-4 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md"><span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-amber-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4"/></svg></span><strong class="mt-3 block">Notifications</strong><span class="text-xs text-stone-500">{{ $dashboardStats['unread_notifications'] }} unread</span></a>
        <a href="#account-settings" class="surface-card group p-4 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md"><span class="grid h-10 w-10 place-items-center rounded-xl bg-stone-100 text-stone-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M4 21a8 8 0 0 1 16 0"/></svg></span><strong class="mt-3 block">Profile</strong><span class="text-xs text-stone-500">Manage details</span></a>
    </nav>

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.45fr)_minmax(300px,.75fr)]">
        <section class="surface-card p-6 sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3"><div><p class="eyebrow">Latest order</p><h2 class="font-display mt-2 text-3xl font-semibold text-brand-950">{{ $latestOrder ? $latestOrder->order_number : 'Your first order awaits' }}</h2></div>@if($latestOrder)<span class="rounded-full bg-brand-100 px-3 py-1.5 text-xs font-black text-brand-800">{{ ucfirst($latestOrder->order_status) }}</span>@endif</div>
            @if($latestOrder)
                <div class="mt-6 h-2 overflow-hidden rounded-full bg-stone-100"><div class="h-full rounded-full {{ $latestOrder->order_status === 'cancelled' ? 'bg-red-500' : 'bg-brand-700' }}" style="width: {{ $orderProgress }}%"></div></div>
                <div class="mt-3 flex justify-between text-[10px] font-bold uppercase tracking-wide text-stone-400"><span>Placed</span><span>Packed</span><span>Shipped</span><span>Delivered</span></div>
                <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-stone-200 pt-5"><div><strong>₹{{ number_format($latestOrder->total_amount, 2) }}</strong><span class="ml-2 text-sm text-stone-500">{{ $latestOrder->items->sum('quantity') }} items · {{ $latestOrder->created_at->format('d M Y') }}</span></div><div class="flex gap-2"><a href="{{ route('orders.show', $latestOrder) }}" class="button-secondary !py-2.5">Details</a><a href="{{ route('orders.track', $latestOrder) }}" class="button-primary !py-2.5">Track order</a></div></div>
            @else
                <p class="mt-4 max-w-xl text-sm leading-7 text-stone-600">Explore traceable grains, pulses, preserves and gift boxes sourced from Himalayan producers.</p><a href="{{ route('products.index') }}" class="button-primary mt-6">Explore the collection</a>
            @endif
        </section>

        <aside class="rounded-[1.5rem] bg-[#e9a11b] p-6 text-brand-950 sm:p-8">
            <p class="text-xs font-black uppercase tracking-[.18em]">Pahadi Roots rewards</p><div class="mt-4 flex items-end justify-between gap-4"><div><strong class="font-display text-5xl font-semibold">{{ number_format($user->loyalty_points) }}</strong><span class="ml-1 font-bold">points</span></div><span class="rounded-full bg-white/50 px-3 py-1 text-xs font-black">Worth ₹{{ number_format($dashboardStats['reward_value'], 2) }}</span></div>
            <p class="mt-4 text-sm leading-6 text-brand-950/80">Use points for up to {{ config('commerce.loyalty.maximum_redemption_percent') }}% off merchandise at checkout.</p>
            <div class="mt-6 grid grid-cols-2 gap-3"><div class="rounded-xl bg-white/35 p-4"><strong class="text-2xl">{{ $referralStats['signups'] }}</strong><span class="block text-xs font-bold">Friends joined</span></div><div class="rounded-xl bg-white/35 p-4"><strong class="text-2xl">{{ $referralStats['qualified'] }}</strong><span class="block text-xs font-bold">Rewards earned</span></div></div>
            <a href="{{ route('rewards') }}" class="mt-5 inline-flex text-sm font-black">See rewards and referrals →</a>
        </aside>
    </div>

    @if($recommendedProducts->isNotEmpty())
        <section class="mt-10"><div class="flex items-end justify-between gap-4"><div><p class="eyebrow">Picked for your pantry</p><h2 class="font-display mt-2 text-3xl font-semibold text-brand-950">Fresh from the hills</h2></div><a href="{{ route('products.index') }}" class="text-sm font-black text-brand-700">Shop all →</a></div><div class="mt-5 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">@foreach($recommendedProducts as $product)<x-product-card :product="$product" :wishlist-product-ids="$wishlistProductIds" />@endforeach</div></section>
    @endif

    <section id="account-settings" class="mt-10 scroll-mt-28">
        <div class="mb-5"><p class="eyebrow">Account settings</p><h2 class="font-display mt-2 text-3xl font-semibold text-brand-950">Personal details and security</h2></div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="surface-card p-6 sm:p-8">@csrf @method('PUT')
            <div class="grid gap-8 lg:grid-cols-[220px_1fr]">
                <div><div class="mx-auto h-32 w-32 overflow-hidden rounded-full border-4 border-white bg-brand-100 shadow-md lg:mx-0"><img id="avatarPreview" @if($user->avatar_url) src="{{ $user->avatar_url }}" @endif alt="profile photo preview" class="h-full w-full object-cover {{ $user->avatar_url ? '' : 'hidden' }}"><div id="avatarFallback" class="grid h-full w-full place-items-center text-4xl font-black text-brand-800 {{ $user->avatar_url ? 'hidden' : '' }}">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</div></div><label for="profileAvatar" class="button-secondary mt-4 cursor-pointer !py-2.5">Choose photo</label><input id="profileAvatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only"><p class="field-help">JPG, PNG or WebP · Max 2 MB</p>@error('avatar')<p class="field-error">{{ $message }}</p>@enderror @if($user->avatar_path)<label class="mt-3 flex cursor-pointer items-center gap-2 text-xs font-bold text-stone-600"><input name="remove_avatar" type="checkbox" value="1"> Remove current photo</label>@endif</div>
                <div><div class="grid gap-5 md:grid-cols-2"><div><label for="profile-name" class="field-label">Name</label><input id="profile-name" name="name" value="{{ old('name',$user->name) }}" class="field-control">@error('name')<p class="field-error">{{ $message }}</p>@enderror</div><div><label for="profile-email" class="field-label">Email</label><input id="profile-email" name="email" type="email" value="{{ old('email',$user->email) }}" class="field-control">@error('email')<p class="field-error">{{ $message }}</p>@enderror</div></div>
                    <details class="mt-6 rounded-2xl border border-stone-200 p-5" @if($errors->hasAny(['current_password','password'])) open @endif><summary class="cursor-pointer font-black text-brand-950">Change password <span class="font-normal text-stone-400">(optional)</span></summary><div class="mt-5 grid gap-4 md:grid-cols-2"><div class="md:col-span-2"><label class="field-label">Current password</label><input name="current_password" type="password" class="field-control">@error('current_password')<p class="field-error">{{ $message }}</p>@enderror</div><div><label class="field-label">New password</label><input name="password" type="password" class="field-control"></div><div><label class="field-label">Confirm password</label><input name="password_confirmation" type="password" class="field-control"></div></div></details>
                    <button class="button-primary mt-6">Save account changes</button>
                </div>
            </div>
        </form>
    </section>

    <section class="surface-card mt-6 overflow-hidden"><div class="border-b border-stone-200 p-6"><h2 class="font-display text-2xl font-semibold">Points activity</h2></div><div class="divide-y divide-stone-200">@forelse($transactions as $transaction)<div class="flex items-center justify-between gap-4 p-5"><div><p class="font-bold">{{ $transaction->description }}</p><p class="text-xs text-stone-500">{{ $transaction->created_at->format('d M Y, h:i A') }}</p></div><div class="text-right"><b class="text-lg {{ $transaction->points>=0?'text-brand-700':'text-red-600' }}">{{ $transaction->points>=0?'+':'' }}{{ $transaction->points }}</b><small class="block text-stone-500">Balance {{ $transaction->balance_after }}</small></div></div>@empty<p class="p-10 text-center text-stone-500">Your points activity will appear here.</p>@endforelse</div><div class="p-4">{{ $transactions->links() }}</div></section>
</div>
@endsection

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
document.getElementById('profileAvatar')?.addEventListener('change', event => {
    const file = event.target.files?.[0];
    if (!file) return;
    const preview = document.getElementById('avatarPreview');
    const fallback = document.getElementById('avatarFallback');
    const previewUrl = URL.createObjectURL(file);
    preview.addEventListener('load', () => URL.revokeObjectURL(previewUrl), { once: true });
    preview.src = previewUrl;
    preview.classList.remove('hidden');
    fallback?.classList.add('hidden');
});
</script>
@endpush
