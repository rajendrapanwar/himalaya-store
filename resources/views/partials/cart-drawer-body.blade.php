{{-- Rendered inside the slide-out cart drawer, and re-rendered after an in-place add to cart. --}}
@php($money = fn ($amount) => '₹'.number_format((float) $amount, 2))
@if($miniCart['count'] > 0)
    <div class="border-b border-stone-200/80 px-6 py-4">
        @if($miniCart['free_shipping_eligible'])
            <p class="flex items-center gap-2 text-sm font-bold text-brand-800"><span class="grid h-6 w-6 place-items-center rounded-full bg-brand-100 text-buransh" aria-hidden="true"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m5 12 4 4L19 6"/></svg></span>You’ve unlocked free delivery</p>
        @else
            <p class="text-sm text-stone-600">Add <strong class="text-brand-900">₹{{ number_format(ceil($miniCart['amount_until_free_shipping']), 0) }}</strong> more for <strong class="text-brand-900">free delivery</strong></p>
        @endif
        <div class="mt-3 h-2 overflow-hidden rounded-full bg-stone-200" role="progressbar" aria-label="Progress toward free delivery" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $miniCart['free_shipping_progress'] }}">
            <div class="h-full rounded-full bg-gradient-to-r from-buransh to-sun transition-[width] duration-700" style="width: {{ $miniCart['free_shipping_progress'] }}%"></div>
        </div>
    </div>
    <ul class="flex-1 space-y-1 overflow-y-auto px-4 py-4" aria-label="Items in your cart">
        @foreach($miniCart['lines'] as $line)
            <li class="stagger-item flex items-center gap-4 rounded-2xl p-2 transition hover:bg-white" style="--i: {{ $loop->index }}">
                <a href="{{ $line['url'] }}" class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-snow">
                    @if($line['image'])<img src="{{ $line['image'] }}" alt="" class="h-full w-full object-cover" loading="lazy" width="80" height="80">@else<span class="grid h-full place-items-center text-brand-300"><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.5" d="m5 18 6-9 3 5 2-3 3 7H5Z"/></svg></span>@endif
                </a>
                <div class="min-w-0 flex-1">
                    @if($line['meta'])<p class="text-[10px] font-black uppercase tracking-widest text-buransh">{{ $line['meta'] }}</p>@endif
                    <a href="{{ $line['url'] }}" class="mt-0.5 line-clamp-2 text-sm font-bold leading-snug text-ink hover:text-buransh">{{ $line['name'] }}</a>
                    <p class="mt-1 text-xs text-stone-500">Qty {{ $line['quantity'] }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <p class="text-sm font-black text-ink">{{ $money($line['line_total']) }}</p>
                    <form method="POST" action="{{ $line['remove_url'] }}">@csrf @method('DELETE')<button type="submit" class="text-[11px] font-bold text-stone-400 underline-offset-2 transition hover:text-red-700 hover:underline" aria-label="Remove {{ $line['name'] }} from cart">Remove</button></form>
                </div>
            </li>
        @endforeach
    </ul>
    <div class="border-t border-stone-200/80 bg-white px-6 pb-6 pt-5">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between text-stone-600"><dt>Subtotal</dt><dd>{{ $money($miniCart['subtotal']) }}</dd></div>
            @if($miniCart['discount'] > 0)<div class="flex justify-between font-bold text-buransh"><dt>Coupon savings</dt><dd>−{{ $money($miniCart['discount']) }}</dd></div>@endif
            <div class="flex justify-between text-base font-black text-ink"><dt>Estimated total</dt><dd>{{ $money($miniCart['total']) }}</dd></div>
        </dl>
        <p class="mt-1 text-xs text-stone-500">Delivery is calculated from your pincode at checkout.</p>
        <div class="mt-5 grid gap-2">
            <a href="{{ route('checkout.index') }}" class="button-primary w-full">Checkout securely <span class="button-arrow" aria-hidden="true">→</span></a>
            <a href="{{ route('cart.index') }}" class="button-secondary w-full">View full cart</a>
        </div>
    </div>
@else
    <div class="flex flex-1 flex-col items-center justify-center px-8 text-center">
        <div class="relative grid h-24 w-24 place-items-center rounded-full bg-brand-50 text-brand-500">
            <svg class="h-10 w-10 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14l-1 13H6L5 8Zm3 2V6a4 4 0 0 1 8 0v4"/></svg>
        </div>
        <p class="font-display mt-6 text-3xl font-medium text-ink">Your basket is empty</p>
        <p class="mt-2 max-w-xs text-sm leading-6 text-stone-500">Mountain honey, heirloom grains and teas are waiting to be discovered.</p>
        <a href="{{ route('products.index') }}" class="button-primary mt-7">Explore the pantry <span class="button-arrow" aria-hidden="true">→</span></a>
    </div>
@endif
