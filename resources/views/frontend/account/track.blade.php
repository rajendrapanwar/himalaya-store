@extends('layouts.app')

@section('title', 'Track '.$order->order_number.' · Pahadi Roots')

@section('content')
@php
    $statusCopy = match ($order->order_status) {
        'processing' => ['Packing your order', 'Our team is checking and packing every item before dispatch.'],
        'shipped' => ['Your parcel is on the way', 'Use the shipment details below for the latest courier update.'],
        'delivered' => ['Delivered with care', 'We hope you enjoy a little piece of the hills.'],
        'cancelled' => ['This order was cancelled', 'No further shipment updates will be added to this order.'],
        default => ['Order received', 'We will update this page as soon as packing begins.'],
    };
@endphp

<div class="mx-auto max-w-6xl py-8 sm:py-12">
    <nav class="mb-6 flex items-center gap-2 text-xs font-bold text-stone-500" aria-label="Breadcrumb">
        <a href="{{ route('profile') }}" class="hover:text-buransh">Account</a><span aria-hidden="true">/</span>
        <a href="{{ route('orders') }}" class="hover:text-buransh">Orders</a><span aria-hidden="true">/</span>
        <span class="text-stone-800">Track order</span>
    </nav>

    <section class="overflow-hidden rounded-[2rem] bg-night text-white shadow-xl shadow-brand-950/10">
        <div class="relative grid gap-8 p-7 sm:p-10 lg:grid-cols-[1fr_auto] lg:items-end">
            <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full border-[42px] border-white/[.04]" aria-hidden="true"></div>
            <div class="relative">
                <p class="text-xs font-black uppercase tracking-[.2em] text-amber-300">Live order status</p>
                <h1 class="font-display mt-4 text-4xl font-semibold sm:text-5xl">{{ $statusCopy[0] }}</h1>
                <p class="mt-4 max-w-2xl leading-7 text-brand-100">{{ $statusCopy[1] }}</p>
            </div>
            <div class="relative rounded-2xl border border-white/10 bg-white/[.07] px-5 py-4 backdrop-blur">
                <span class="block text-[10px] font-black uppercase tracking-widest text-brand-200">Order number</span>
                <strong class="mt-1 block text-lg">{{ $order->order_number }}</strong>
                <span class="mt-1 block text-xs text-brand-200">Placed {{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
        </div>
    </section>

    @if($order->order_status === 'cancelled')
        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-900">
            <strong>Cancelled {{ $order->cancelled_at?->format('d M Y, h:i A') }}</strong>
            <p class="mt-1 text-sm text-red-700">If a payment was captured, the refund status will be shown in your order details.</p>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        <main class="space-y-6">
            <section class="surface-card p-6 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div><p class="eyebrow">Journey to your doorstep</p><h2 class="font-display mt-2 text-3xl font-semibold text-ink">Delivery progress</h2></div>
                    <span class="rounded-full bg-brand-100 px-3 py-1.5 text-xs font-black text-brand-800">{{ ucfirst($order->order_status) }}</span>
                </div>

                <ol class="mt-8 grid gap-0 md:grid-cols-4" aria-label="Order progress">
                    @foreach($trackingSteps as $step)
                        <li class="relative flex gap-4 pb-7 last:pb-0 md:block md:pb-0">
                            @unless($loop->last)<span class="absolute left-[17px] top-9 h-[calc(100%-1.25rem)] w-0.5 {{ $step['state'] === 'complete' ? 'bg-brand-700' : 'bg-stone-200' }} md:left-[36px] md:top-[17px] md:h-0.5 md:w-[calc(100%-36px)]" aria-hidden="true"></span>@endunless
                            <span class="relative z-10 grid h-9 w-9 shrink-0 place-items-center rounded-full border-2 {{ $step['state'] === 'complete' ? 'border-brand-700 bg-brand-700 text-white' : ($step['state'] === 'current' ? 'border-saffron bg-amber-50 text-buransh ring-4 ring-amber-100' : 'border-stone-200 bg-white text-stone-400') }}">
                                @if($step['state'] === 'complete')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 12 4 4L19 6"/></svg>
                                @else<span class="text-xs font-black">{{ $loop->iteration }}</span>@endif
                            </span>
                            <div class="min-w-0 md:mt-4 md:pr-5">
                                <h3 class="font-extrabold text-stone-900">{{ $step['title'] }}</h3>
                                <p class="mt-1 text-xs leading-5 text-stone-500">{{ $step['description'] }}</p>
                                @if($step['time'])<time class="mt-2 block text-[11px] font-bold text-buransh">{{ $step['time']->format('d M, h:i A') }}</time>@endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>

            @if($order->tracking_number)
                <section class="surface-card p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-5">
                        <div><p class="eyebrow">Courier details</p><h2 class="mt-2 text-xl font-black">{{ $order->courier_name ?: 'Delivery partner' }}</h2></div>
                        <button type="button" data-copy-value="{{ $order->tracking_number }}" class="button-secondary !py-2.5">Copy tracking number</button>
                    </div>
                    <div class="mt-5 rounded-xl border border-dashed border-brand-300 bg-brand-50 p-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-buransh">Tracking number</span>
                        <p class="mt-1 break-all font-mono text-lg font-bold text-ink">{{ $order->tracking_number }}</p>
                    </div>
                </section>
            @elseif(in_array($order->order_status, ['pending', 'processing'], true))
                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                    <h2 class="font-black text-amber-950">Tracking details will appear after dispatch</h2>
                    <p class="mt-2 text-sm leading-6 text-amber-800">You will also receive a notification when the courier and tracking number are assigned.</p>
                </section>
            @endif

            @if($order->statusHistory->isNotEmpty())
                <details class="surface-card group p-6">
                    <summary class="flex cursor-pointer list-none items-center justify-between font-black text-ink">All order updates <span class="text-xl transition group-open:rotate-45" aria-hidden="true">＋</span></summary>
                    <div class="mt-5 space-y-4 border-t border-stone-200 pt-5">
                        @foreach($order->statusHistory->sortByDesc('created_at') as $event)
                            <div class="flex gap-3"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-brand-600"></span><div><strong class="text-sm">{{ ucfirst($event->to_status) }}</strong><p class="mt-0.5 text-xs leading-5 text-stone-500">{{ $event->created_at->format('d M Y, h:i A') }}{{ $event->note ? ' · '.$event->note : '' }}</p></div></div>
                        @endforeach
                    </div>
                </details>
            @endif
        </main>

        <aside class="space-y-6">
            <section class="surface-card p-6">
                <h2 class="text-lg font-black">Delivery address</h2>
                <p class="mt-4 text-sm leading-7 text-stone-600">{{ $order->address->first_name }} {{ $order->address->last_name }}<br>{{ $order->address->address_line_1 }}<br>@if($order->address->address_line_2){{ $order->address->address_line_2 }}<br>@endif{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->pincode }}<br>{{ $order->address->phone }}</p>
            </section>
            <section class="surface-card p-6">
                <div class="flex items-center justify-between"><h2 class="text-lg font-black">Order summary</h2><span class="text-xs font-bold text-stone-500">{{ $order->items->sum('quantity') }} items</span></div>
                <div class="mt-4 space-y-3 border-y border-stone-200 py-4">
                    @foreach($order->items->take(3) as $item)<div class="flex justify-between gap-4 text-sm"><span class="line-clamp-1">{{ $item->product_name }} × {{ $item->quantity }}</span><strong>₹{{ number_format($item->total, 2) }}</strong></div>@endforeach
                    @if($order->items->count() > 3)<p class="text-xs font-bold text-stone-500">+ {{ $order->items->count() - 3 }} more products</p>@endif
                </div>
                <div class="mt-4 flex justify-between text-lg font-black"><span>Total</span><span>₹{{ number_format($order->total_amount, 2) }}</span></div>
                <a href="{{ route('orders.show', $order) }}" class="button-secondary mt-5 w-full">View full order</a>
            </section>
            <section class="rounded-2xl bg-stone-100 p-6">
                <h2 class="font-black text-ink">Need help with this order?</h2>
                <p class="mt-2 text-sm leading-6 text-stone-600">Our Dehradun support team can help with delivery or product questions.</p>
                <a href="{{ route('contact') }}" class="mt-4 inline-flex text-sm font-black text-buransh">Contact support →</a>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
document.querySelector('[data-copy-value]')?.addEventListener('click', async event => {
    const button = event.currentTarget;
    try {
        await navigator.clipboard.writeText(button.dataset.copyValue);
        const original = button.textContent;
        button.textContent = 'Copied';
        window.setTimeout(() => button.textContent = original, 1600);
    } catch (_) {
        window.prompt('Copy tracking number', button.dataset.copyValue);
    }
});
</script>
@endpush
