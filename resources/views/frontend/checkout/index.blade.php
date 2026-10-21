@extends('layouts.app')
@section('title', 'Secure checkout · Pahadi Roots')

@section('content')
<header class="mb-8"><p class="eyebrow">Secure checkout</p><h1 class="section-title mt-3">Delivery and payment</h1><ol class="mt-5 flex max-w-lg items-center text-xs font-bold text-stone-500" aria-label="Checkout progress"><li class="text-buransh">Cart</li><li class="mx-3 h-px flex-1 bg-brand-300"></li><li class="text-buransh">Delivery & payment</li><li class="mx-3 h-px flex-1 bg-stone-300"></li><li>Confirmation</li></ol></header>

<form method="POST" action="{{ route('checkout.placeOrder') }}" class="grid gap-8 lg:grid-cols-[1fr_390px]">@csrf
    <div class="space-y-5">
        <section class="surface-card p-5 sm:p-7"><div class="flex items-center gap-3"><span class="grid h-8 w-8 place-items-center rounded-full bg-brand-800 text-sm font-black text-white">1</span><h2 class="font-display text-2xl font-semibold">Contact details</h2></div><div class="mt-6 grid gap-5 sm:grid-cols-2">
            <div><label for="firstName" class="field-label">First name</label><input id="firstName" name="first_name" value="{{ old('first_name', $checkoutDefaults['first_name']) }}" autocomplete="given-name" maxlength="255" class="field-control" required aria-invalid="{{ $errors->has('first_name') ? 'true' : 'false' }}">@error('first_name')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="lastName" class="field-label">Last name <span class="font-normal text-stone-500">(optional)</span></label><input id="lastName" name="last_name" value="{{ old('last_name', $checkoutDefaults['last_name']) }}" autocomplete="family-name" maxlength="255" class="field-control"></div>
            <div><label for="checkoutEmail" class="field-label">Email address</label><input id="checkoutEmail" name="email" type="email" value="{{ old('email', $checkoutDefaults['email']) }}" autocomplete="email" maxlength="255" class="field-control" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="checkoutPhone" class="field-label">Mobile number</label><input id="checkoutPhone" name="phone" type="tel" value="{{ old('phone', $checkoutDefaults['phone']) }}" autocomplete="tel" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10-digit mobile number" class="field-control" required aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}">@error('phone')<p class="field-error">{{ $message }}</p>@enderror</div>
        </div></section>

        <section class="surface-card p-5 sm:p-7"><div class="flex items-center gap-3"><span class="grid h-8 w-8 place-items-center rounded-full bg-brand-800 text-sm font-black text-white">2</span><h2 class="font-display text-2xl font-semibold">Delivery address</h2></div><div class="mt-6 grid gap-5">
            <div><label for="addressOne" class="field-label">Address line 1</label><input id="addressOne" name="address_line_1" value="{{ old('address_line_1', $checkoutDefaults['address_line_1']) }}" autocomplete="address-line1" maxlength="255" placeholder="House number, street or village" class="field-control" required aria-invalid="{{ $errors->has('address_line_1') ? 'true' : 'false' }}">@error('address_line_1')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div><label for="addressTwo" class="field-label">Address line 2 <span class="font-normal text-stone-500">(optional)</span></label><input id="addressTwo" name="address_line_2" value="{{ old('address_line_2', $checkoutDefaults['address_line_2']) }}" autocomplete="address-line2" maxlength="255" placeholder="Landmark, apartment or locality" class="field-control"></div>
            <div class="grid gap-5 sm:grid-cols-3"><div><label for="city" class="field-label">City</label><input id="city" name="city" value="{{ old('city', $checkoutDefaults['city']) }}" autocomplete="address-level2" maxlength="255" class="field-control" required>@error('city')<p class="field-error">{{ $message }}</p>@enderror</div><div><label for="state" class="field-label">State</label><input id="state" name="state" value="{{ old('state', $checkoutDefaults['state']) }}" autocomplete="address-level1" maxlength="255" class="field-control" required>@error('state')<p class="field-error">{{ $message }}</p>@enderror</div><div><label for="checkoutPincode" class="field-label">Pincode</label><input id="checkoutPincode" name="pincode" value="{{ old('pincode', $checkoutDefaults['pincode']) }}" autocomplete="postal-code" inputmode="numeric" pattern="[1-9][0-9]{5}" maxlength="6" class="field-control" required aria-invalid="{{ $errors->has('pincode') ? 'true' : 'false' }}">@error('pincode')<p class="field-error">{{ $message }}</p>@enderror</div></div>
            <div id="checkoutDeliveryStatus" role="status" aria-live="polite" class="rounded-lg p-3 text-xs leading-5 {{ $destination ? 'border border-brand-200 bg-brand-50 text-brand-800' : 'bg-stone-100 text-stone-600' }}">
                @if($destination)
                    <strong>Delivery available to {{ $destination->city }}, {{ $destination->state }}.</strong> Estimated {{ $destination->estimated_days_min }}–{{ $destination->estimated_days_max }} business days. {{ $destination->cod_available ? 'COD is available.' : 'Online payment is required.' }}
                @else
                    Enter a serviceable six-digit pincode to calculate delivery and your final total.
                @endif
            </div>
        </div></section>

        <section class="surface-card p-5 sm:p-7"><div class="flex items-center gap-3"><span class="grid h-8 w-8 place-items-center rounded-full bg-brand-800 text-sm font-black text-white">3</span><h2 class="font-display text-2xl font-semibold">Payment method</h2></div><div class="mt-6 grid gap-3" id="paymentOptions">
            <label class="payment-option flex cursor-pointer items-start gap-4 rounded-xl border border-stone-200 p-4 transition hover:border-brand-400"><input type="radio" name="payment_method" value="stripe" class="mt-1" @checked(old('payment_method', 'stripe') === 'stripe') required><span><strong class="block text-sm">Pay online with Stripe</strong><span class="mt-1 block text-xs leading-5 text-stone-500">Use UPI, debit card or credit card through Stripe’s secure checkout.</span><span class="mt-3 inline-flex gap-2 text-[10px] font-black uppercase tracking-wider text-buransh"><span class="rounded bg-brand-50 px-2 py-1">UPI</span><span class="rounded bg-brand-50 px-2 py-1">Visa</span><span class="rounded bg-brand-50 px-2 py-1">Mastercard</span></span></span></label>
            <label class="payment-option flex cursor-pointer items-start gap-4 rounded-xl border border-stone-200 p-4 transition hover:border-brand-400"><input type="radio" name="payment_method" value="cod" class="mt-1" @checked(old('payment_method') === 'cod') required><span><strong class="block text-sm">Cash on delivery</strong><span class="mt-1 block text-xs leading-5 text-stone-500">Available only for eligible delivery pincodes. Pay when the order arrives.</span></span></label>
            @error('payment_method')<p class="field-error">{{ $message }}</p>@enderror
        </div></section>

        <section class="surface-card p-5 sm:p-7"><label for="orderNote" class="font-display text-xl font-semibold">Order note <span class="font-sans text-xs font-normal text-stone-500">(optional)</span></label><textarea id="orderNote" name="customer_note" rows="3" maxlength="1000" class="field-control mt-4" placeholder="Packing or delivery instructions">{{ old('customer_note') }}</textarea></section>
    </div>

    <aside><div class="surface-card p-6 lg:sticky lg:top-32"><h2 class="font-display text-2xl font-semibold">Order summary</h2><div class="mt-5 max-h-64 space-y-3 overflow-y-auto pr-1">
        @foreach($cart->items as $item)<div class="flex justify-between gap-4 text-xs"><span class="text-stone-600">{{ $item->product->name }} × {{ $item->quantity }}</span><strong>₹{{ number_format((float) ($item->price * $item->quantity), 2) }}</strong></div>@endforeach
        @foreach($cart->bundleItems as $item)<div class="flex justify-between gap-4 text-xs"><span class="text-stone-600">{{ $item->bundle->name }} × {{ $item->quantity }}</span><strong>₹{{ number_format((float) ($item->bundle->price * $item->quantity), 2) }}</strong></div>@endforeach
    </div><dl class="mt-6 space-y-3 border-t border-stone-200 pt-5 text-sm"><div class="flex justify-between text-stone-600"><dt>Subtotal</dt><dd>₹{{ number_format((float) $subtotal, 2) }}</dd></div>@if($coupon)<div class="flex justify-between font-bold text-buransh"><dt>Coupon · {{ $coupon->code }}</dt><dd>−₹{{ number_format((float) $discountAmount, 2) }}</dd></div>@endif<div class="flex justify-between text-stone-600"><dt>Delivery</dt><dd id="checkoutShipping">@if($destination){{ (float) $shippingAmount > 0 ? '₹'.number_format((float) $shippingAmount, 2) : 'Free' }}@else Enter pincode @endif</dd></div><div id="loyaltyDiscountRow" class="{{ (float) $loyaltyDiscount > 0 ? 'flex' : 'hidden' }} justify-between font-bold text-buransh"><dt>Loyalty points</dt><dd id="loyaltyDiscountValue">@if((float) $loyaltyDiscount > 0)−₹{{ number_format((float) $loyaltyDiscount, 2) }}@endif</dd></div><div class="flex justify-between border-t border-stone-200 pt-4 text-lg font-black"><dt>Total payable</dt><dd id="checkoutEstimate">{{ $destination ? '₹'.number_format((float) $total, 2) : '—' }}</dd></div></dl>
        <div class="mt-5 rounded-xl bg-brand-50 p-4"><label for="loyaltyPoints" class="field-label">Redeem loyalty points</label><input id="loyaltyPoints" name="loyalty_points" type="number" min="0" max="{{ auth()->user()->loyalty_points }}" value="{{ old('loyalty_points', 0) }}" class="field-control"><p class="field-help">{{ auth()->user()->loyalty_points }} available. Redemption is capped at {{ config('commerce.loyalty.maximum_redemption_percent') }}% of eligible merchandise value.</p>@error('loyalty_points')<p class="field-error">{{ $message }}</p>@enderror</div>
        <button id="placeOrderButton" class="button-primary mt-6 w-full" type="submit">Place order securely</button><p class="mt-4 text-center text-[11px] leading-5 text-stone-500">This total includes the delivery charge for your pincode. It is recalculated securely on the server when the order is placed.</p>
    </div></aside>
</form>
@endsection

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
(() => {
    const options = document.querySelectorAll('.payment-option');
    const refreshOptions = () => options.forEach(option => { const checked = option.querySelector('input').checked; option.classList.toggle('border-brand-600', checked); option.classList.toggle('bg-brand-50', checked); });
    options.forEach(option => option.querySelector('input').addEventListener('change', refreshOptions)); refreshOptions();
    const points = document.getElementById('loyaltyPoints');
    const row = document.getElementById('loyaltyDiscountRow');
    const value = document.getElementById('loyaltyDiscountValue');
    const estimate = document.getElementById('checkoutEstimate');
    const shipping = document.getElementById('checkoutShipping');
    const pincode = document.getElementById('checkoutPincode');
    const status = document.getElementById('checkoutDeliveryStatus');
    const placeOrder = document.getElementById('placeOrderButton');
    const checkoutForm = placeOrder.closest('form');
    const cod = document.querySelector('input[name="payment_method"][value="cod"]');
    const stripe = document.querySelector('input[name="payment_method"][value="stripe"]');
    const money = (amount) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: 2 }).format(Number(amount));
    let quoteTimer;
    let quoteRequest;

    const setStatus = (message, type = 'neutral') => {
        status.textContent = message;
        status.className = `rounded-lg border p-3 text-xs leading-5 ${type === 'success' ? 'border-brand-200 bg-brand-50 text-brand-800' : type === 'error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-stone-200 bg-stone-100 text-stone-600'}`;
    };

    const refreshQuote = async () => {
        const code = pincode.value.trim();
        if (!/^[1-9][0-9]{5}$/.test(code)) {
            quoteRequest?.abort();
            shipping.textContent = 'Enter pincode';
            estimate.textContent = '—';
            placeOrder.disabled = true;
            cod.disabled = false;
            setStatus('Enter a valid six-digit pincode to calculate delivery and the total payable.');
            return;
        }

        quoteRequest?.abort();
        quoteRequest = new AbortController();
        placeOrder.disabled = true;
        shipping.textContent = 'Calculating…';
        estimate.textContent = 'Calculating…';
        setStatus('Checking delivery and recalculating your order…');

        const payload = new FormData();
        payload.append('_token', checkoutForm.querySelector('input[name="_token"]').value);
        payload.append('pincode', code);
        payload.append('loyalty_points', points?.value || '0');

        try {
            const response = await fetch(@json(route('checkout.quote')), { method: 'POST', body: payload, headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, signal: quoteRequest.signal });
            const data = await response.json();
            if (!response.ok) throw new Error(data.errors?.pincode?.[0] || data.errors?.loyalty_points?.[0] || data.message || 'Unable to calculate delivery.');

            shipping.textContent = Number(data.shipping) > 0 ? money(data.shipping) : 'Free';
            const loyalty = Number(data.loyalty_discount);
            row.classList.toggle('hidden', loyalty <= 0);
            row.classList.toggle('flex', loyalty > 0);
            value.textContent = loyalty > 0 ? `−${money(loyalty)}` : '';
            estimate.textContent = money(data.total);
            cod.disabled = !data.cod_available;
            if (!data.cod_available && cod.checked) {
                stripe.checked = true;
                refreshOptions();
            }
            const deliveryOffer = data.free_shipping ? ` Free delivery is applied for orders over ${money(data.free_shipping_threshold)}.` : '';
            setStatus(`Delivery available to ${data.location}. Estimated ${data.estimate}.${deliveryOffer} ${data.cod_available ? 'COD is available.' : 'Online payment is required.'}`, 'success');
            placeOrder.disabled = false;
        } catch (error) {
            if (error.name === 'AbortError') return;
            shipping.textContent = 'Unavailable';
            estimate.textContent = '—';
            cod.disabled = true;
            if (cod.checked) { stripe.checked = true; refreshOptions(); }
            setStatus(error.message || 'Delivery is not available for this pincode.', 'error');
        }
    };

    const queueQuote = () => { window.clearTimeout(quoteTimer); quoteTimer = window.setTimeout(refreshQuote, 350); };
    pincode?.addEventListener('input', queueQuote);
    pincode?.addEventListener('change', refreshQuote);
    points?.addEventListener('input', queueQuote);
    refreshQuote();
})();
</script>
@endpush
