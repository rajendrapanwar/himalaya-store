@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold">{{ $order->order_number }}</h2>
            <p class="text-gray-500">
                Placed on {{ $order->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="px-4 py-2 rounded-lg border">
            Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-bold">Order Items</h3>
                </div>

                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-4">Product</th>
                            <th class="text-left p-4">Qty</th>
                            <th class="text-left p-4">Price</th>
                            <th class="text-right p-4">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-t">
                                <td class="p-4">
                                    <div class="font-semibold">{{ $item->product_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->product_sku }}</div>
                                </td>

                                <td class="p-4">{{ $item->quantity }}</td>

                                <td class="p-4">
                                    ₹{{ number_format($item->price, 2) }}
                                </td>

                                <td class="p-4 text-right font-bold">
                                    ₹{{ number_format($item->total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h3 class="text-xl font-bold mb-4">Customer Note</h3>

                <p class="text-gray-600">
                    {{ $order->customer_note ?: 'No customer note.' }}
                </p>
            </div>

        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h3 class="text-xl font-bold mb-4">Update Status</h3>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Order Status</label>
                        <select name="order_status" class="w-full rounded-xl border-gray-300">
                            @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ $order->order_status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Payment Status</label>
                        <select name="payment_status" class="w-full rounded-xl border-gray-300">
                            @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                                <option value="{{ $status }}" {{ $order->payment_status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="w-full bg-green-600 text-white px-4 py-3 rounded-xl font-bold">
                        Update Order
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h3 class="text-xl font-bold mb-4">Customer Details</h3>

                <p class="font-semibold">
                    {{ $order->address->first_name ?? '' }} {{ $order->address->last_name ?? '' }}
                </p>

                <p class="text-gray-600 mt-2">{{ $order->address->email ?? '' }}</p>
                <p class="text-gray-600">{{ $order->address->phone ?? '' }}</p>
            </div>

            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h3 class="text-xl font-bold mb-4">Shipping Address</h3>

                <p class="text-gray-600 leading-7">
                    {{ $order->address->address_line_1 ?? '' }}<br>
                    @if(!empty($order->address->address_line_2))
                        {{ $order->address->address_line_2 }}<br>
                    @endif
                    {{ $order->address->city ?? '' }},
                    {{ $order->address->state ?? '' }} -
                    {{ $order->address->pincode ?? '' }}<br>
                    {{ $order->address->country ?? '' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h3 class="text-xl font-bold mb-4">Order Summary</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span>₹{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between border-t pt-3 text-lg font-bold">
                        <span>Total</span>
                        <span>₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection