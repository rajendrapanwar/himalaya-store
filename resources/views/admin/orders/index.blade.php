@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

    <div class="p-6 border-b">
        <h2 class="text-xl font-bold">Orders</h2>
        <p class="text-gray-500 text-sm">Manage customer orders and payment status.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-4">Order</th>
                    <th class="text-left p-4">Customer</th>
                    <th class="text-left p-4">Total</th>
                    <th class="text-left p-4">Order Status</th>
                    <th class="text-left p-4">Payment</th>
                    <th class="text-right p-4">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($orders as $order)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-4">
                            <div class="font-bold">{{ $order->order_number }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $order->created_at->format('d M Y, h:i A') }}
                            </div>
                        </td>

                        <td class="p-4">
                            <div class="font-semibold">
                                {{ $order->address->first_name ?? '' }} {{ $order->address->last_name ?? '' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $order->address->email ?? '' }}
                            </div>
                        </td>

                        <td class="p-4 font-bold">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </td>

                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>

                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>

                        <td class="p-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="px-4 py-2 bg-gray-900 text-white rounded-lg">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-12 text-gray-500">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">
        {{ $orders->links() }}
    </div>

</div>

@endsection