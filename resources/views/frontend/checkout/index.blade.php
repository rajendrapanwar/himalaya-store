@extends('layouts.app')

@section('content')

@php
    $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
@endphp

<div class="space-y-10">

    <div>
        <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">
            Secure Checkout
        </p>

        <h1 class="mt-2 text-4xl font-black text-stone-950">
            Checkout
        </h1>
    </div>

    <form method="POST" action="{{ route('checkout.placeOrder') }}" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        @csrf

        <div class="lg:col-span-2 space-y-6">

            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black mb-6">
                    Contact Details
                </h2>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold mb-2">First Name</label>
                        <input name="first_name" value="{{ old('first_name') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                        @error('first_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Last Name</label>
                        <input name="last_name" value="{{ old('last_name') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Email</label>
                        <input name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                        @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black mb-6">
                    Shipping Address
                </h2>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-bold mb-2">Address Line 1</label>
                        <input name="address_line_1" value="{{ old('address_line_1') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                        @error('address_line_1') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Address Line 2</label>
                        <input name="address_line_2" value="{{ old('address_line_2') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-bold mb-2">City</label>
                            <input name="city" value="{{ old('city') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                            @error('city') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">State</label>
                            <input name="state" value="{{ old('state') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                            @error('state') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Pincode</label>
                            <input name="pincode" value="{{ old('pincode') }}" class="w-full rounded-2xl border-stone-200 px-5 py-4">
                            @error('pincode') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black mb-6">
                    Payment Method
                </h2>

                <label class="flex cursor-pointer items-center gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <div>
                        <p class="font-bold text-stone-950">Cash on Delivery</p>
                        <p class="text-sm text-stone-500">Pay when your order arrives.</p>
                    </div>
                </label>
            </div>

            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black mb-4">
                    Order Notes
                </h2>

                <textarea name="customer_note" rows="4" class="w-full rounded-2xl border-stone-200 px-5 py-4" placeholder="Any special instructions?">{{ old('customer_note') }}</textarea>
            </div>

        </div>

        <aside>
            <div class="sticky top-6 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black">
                    Order Summary
                </h2>

                <div class="mt-6 space-y-4">
                    @foreach($cart->items as $item)
                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-stone-600">
                                {{ $item->product->name }} × {{ $item->quantity }}
                            </span>

                            <span class="font-bold">
                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 border-t pt-5 space-y-3">
                    <div class="flex justify-between text-stone-600">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-stone-600">
                        <span>Shipping</span>
                        <span>₹0.00</span>
                    </div>

                    <div class="flex justify-between border-t pt-4 text-xl font-black">
                        <span>Total</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                </div>

                <button class="mt-6 w-full rounded-2xl bg-emerald-600 px-6 py-4 font-bold text-white hover:bg-emerald-700">
                    Place Order
                </button>
            </div>
        </aside>

    </form>

</div>

@endsection