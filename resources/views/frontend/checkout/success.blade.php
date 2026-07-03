@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-3xl">

    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-10 text-center">
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-600 text-4xl text-white">
            ✓
        </div>

        <h1 class="text-4xl font-black text-stone-950">
            Order Placed Successfully
        </h1>

        <p class="mt-3 text-stone-600">
            Thank you for shopping with Himalaya Store.
        </p>

        <div class="mt-6 rounded-2xl bg-white p-5">
            <p class="text-sm text-stone-500">Order Number</p>
            <p class="text-2xl font-black text-stone-950">
                {{ $order->order_number }}
            </p>
        </div>

        <a href="{{ route('products.index') }}"
           class="mt-8 inline-block rounded-full bg-emerald-600 px-8 py-4 font-bold text-white hover:bg-emerald-700">
            Continue Shopping
        </a>
    </div>

</div>

@endsection