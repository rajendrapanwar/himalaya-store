@extends('layouts.app')

@section('content')

@php
    $subtotal = $cart->items->sum(function ($item) {
        return $item->price * $item->quantity;
    });
@endphp

<div class="space-y-10">

    <div>
        <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">
            Shopping Cart
        </p>

        <h1 class="mt-2 text-4xl font-black text-stone-950">
            Your Cart
        </h1>
    </div>

    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 p-4 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl bg-red-50 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($cart->items->count())

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            <div class="lg:col-span-2 space-y-4">

                @foreach($cart->items as $item)

                    @php
                        $product = $item->product;
                        $image = $product->images->where('is_main', 1)->first() ?? $product->images->first();
                        $lineTotal = $item->price * $item->quantity;
                    @endphp

                    <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-5 md:flex-row">

                            <a href="{{ route('products.show', $product->slug) }}" class="h-32 w-full overflow-hidden rounded-2xl bg-stone-100 md:w-32">
                                @if($image)
                                    <img src="{{ asset('storage/' . $image->image) }}"
                                         class="h-full w-full object-cover">
                                @endif
                            </a>

                            <div class="flex-1">
                                <div class="flex flex-col justify-between gap-4 md:flex-row">
                                    <div>
                                        <p class="text-sm font-semibold text-emerald-700">
                                            {{ $product->category->name ?? '' }}
                                        </p>

                                        <h3 class="mt-1 text-xl font-bold text-stone-950">
                                            {{ $product->name }}
                                        </h3>

                                        <p class="mt-2 text-sm text-stone-500">
                                            ₹{{ number_format($item->price, 2) }} each
                                        </p>
                                    </div>

                                    <div class="text-left md:text-right">
                                        <p class="text-xl font-black">
                                            ₹{{ number_format($lineTotal, 2) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                                    <form method="POST" action="{{ route('cart.update', $item) }}" class="flex gap-2">
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="number"
                                            name="quantity"
                                            value="{{ $item->quantity }}"
                                            min="1"
                                            max="{{ $product->stock_quantity }}"
                                            class="w-24 rounded-xl border-stone-200 px-3 py-2 text-center"
                                        >

                                        <button class="rounded-xl bg-stone-900 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                                            Update
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('cart.remove', $item) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button class="rounded-xl border border-red-200 px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                @endforeach

            </div>

            <aside class="lg:col-span-1">
                <div class="sticky top-6 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black">
                        Order Summary
                    </h2>

                    <div class="mt-6 space-y-4">
                        <div class="flex justify-between text-stone-600">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between text-stone-600">
                            <span>Shipping</span>
                            <span>Calculated later</span>
                        </div>

                        <div class="border-t pt-4 flex justify-between text-xl font-black">
                            <span>Total</span>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <a href="#"
                       class="mt-6 block rounded-2xl bg-emerald-600 px-6 py-4 text-center font-bold text-white hover:bg-emerald-700">
                        Continue to Checkout
                    </a>

                    <a href="{{ route('products.index') }}"
                       class="mt-3 block rounded-2xl border border-stone-200 px-6 py-4 text-center font-bold hover:bg-stone-50">
                        Continue Shopping
                    </a>
                </div>
            </aside>

        </div>

    @else

        <div class="rounded-3xl border border-dashed border-stone-300 bg-white p-16 text-center">
            <h2 class="text-2xl font-black text-stone-950">
                Your cart is empty
            </h2>

            <p class="mt-2 text-stone-500">
                Start adding some authentic Himalayan products.
            </p>

            <a href="{{ route('products.index') }}"
               class="mt-6 inline-block rounded-full bg-emerald-600 px-7 py-3 font-bold text-white hover:bg-emerald-700">
                Shop Products
            </a>
        </div>

    @endif

</div>

@endsection