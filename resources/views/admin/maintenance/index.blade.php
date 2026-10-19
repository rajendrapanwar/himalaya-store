@extends('layouts.admin')
@section('title', 'Maintenance mode')

@section('content')
@php($expectedReturn = $maintenance['expected_return_at'] ? \Carbon\Carbon::parse($maintenance['expected_return_at'])->format('Y-m-d\TH:i') : '')
<div class="space-y-6">
    <div class="admin-page-intro"><div><p class="eyebrow">Storefront availability</p><h2>Maintenance mode</h2><p>Temporarily replace the customer storefront with a branded service notice while administrators retain access.</p></div><span class="admin-badge {{ $maintenance['enabled'] ? 'bg-amber-100 text-amber-900' : 'bg-brand-100 text-brand-800' }}"><span class="mr-2 h-2 w-2 rounded-full {{ $maintenance['enabled'] ? 'animate-pulse bg-amber-600' : 'bg-brand-600' }}"></span>{{ $maintenance['enabled'] ? 'Maintenance active' : 'Storefront live' }}</span></div>

    <section class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <form method="POST" action="{{ route('admin.maintenance.update') }}" class="admin-panel">@csrf @method('PATCH')<input type="hidden" name="enabled" value="1">
            <header class="admin-panel-header"><div><h3 class="text-lg font-black text-brand-950">Customer notice</h3><p class="mt-1 text-xs text-stone-500">This message appears on every storefront page while maintenance is enabled.</p></div><svg class="h-7 w-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12M7.76 16.24l-2.12 2.12M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/></svg></header>
            <div class="space-y-5 p-6">
                <div><label for="maintenanceMessage">Maintenance message</label><textarea id="maintenanceMessage" name="message" maxlength="500" rows="5" required>{{ old('message', $maintenance['message']) }}</textarea><p class="mt-2 text-xs leading-5 text-stone-500">Be clear and reassuring. Avoid publishing internal technical details or exact promises you may not be able to keep.</p>@error('message')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div><label for="expectedReturn">Expected return time <span class="font-normal text-stone-500">(optional)</span></label><input id="expectedReturn" name="expected_return_at" type="datetime-local" min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}" value="{{ old('expected_return_at', $expectedReturn) }}"><p class="mt-2 text-xs text-stone-500">Shown in India Standard Time on the customer page.</p>@error('expected_return_at')<p class="field-error">{{ $message }}</p>@enderror</div>
            </div>
            <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-200 bg-stone-50 p-5"><p class="max-w-md text-xs leading-5 text-stone-500">Admin login, admin pages, password recovery, health checks and Stripe webhooks remain available.</p><button type="submit" class="admin-action admin-action-primary" onclick="return confirm('Enable maintenance mode for all storefront visitors?')">{{ $maintenance['enabled'] ? 'Update maintenance page' : 'Enable maintenance mode' }}</button></footer>
        </form>

        <aside class="space-y-5">
            <section class="admin-panel p-6"><div class="flex items-start gap-4"><span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl {{ $maintenance['enabled'] ? 'bg-amber-100 text-amber-800' : 'bg-brand-100 text-brand-700' }}"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/></svg></span><div><h3 class="font-black text-brand-950">Current status</h3><p class="mt-2 text-sm leading-6 text-stone-600">{{ $maintenance['enabled'] ? 'The customer storefront currently returns a temporary 503 response. Search engines are instructed not to index the notice.' : 'Customers and search engines can access the storefront normally.' }}</p></div></div></section>

            @if($maintenance['enabled'])
            <form method="POST" action="{{ route('admin.maintenance.update') }}" class="rounded-2xl border border-brand-200 bg-brand-50 p-6">@csrf @method('PATCH')<input type="hidden" name="enabled" value="0"><h3 class="font-black text-brand-950">Ready to reopen?</h3><p class="mt-2 text-sm leading-6 text-brand-800">Disable maintenance after checking the homepage, catalogue, checkout dependencies and payment configuration.</p><button type="submit" class="admin-action admin-action-primary mt-5 w-full" onclick="return confirm('Make the storefront available to customers now?')">Disable maintenance and go live</button></form>
            @endif

            <section class="rounded-2xl border border-stone-200 bg-white p-6"><h3 class="font-black text-brand-950">Before enabling</h3><ul class="mt-4 space-y-3 text-sm text-stone-600"><li class="flex gap-2"><span class="font-black text-brand-700">1.</span> Finish or pause active deployments.</li><li class="flex gap-2"><span class="font-black text-brand-700">2.</span> Keep workers available for payment webhooks.</li><li class="flex gap-2"><span class="font-black text-brand-700">3.</span> Disable the mode as soon as customer traffic is safe.</li></ul></section>
        </aside>
    </section>
</div>
@endsection
