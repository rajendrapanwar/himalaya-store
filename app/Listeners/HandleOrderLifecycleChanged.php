<?php

namespace App\Listeners;

use App\Events\OrderLifecycleChanged;
use App\Models\Order;
use App\Notifications\AdminAlertNotification;
use App\Notifications\OrderUpdateNotification;
use App\Services\StaffNotificationService;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;

class HandleOrderLifecycleChanged implements ShouldQueueAfterCommit
{
    public function __construct(private readonly StaffNotificationService $staff) {}

    public function handle(OrderLifecycleChanged $event): void
    {
        $order = Order::with('user')->find($event->orderId);
        if (! $order) {
            return;
        }

        $order->user?->notify(new OrderUpdateNotification($order->id, $event->event));

        $copy = match ($event->event) {
            'paid' => ['Payment confirmed', "Payment was confirmed for {$order->order_number}."],
            'placed' => ['New COD order', "{$order->order_number} was placed using cash on delivery."],
            'payment_failed' => ['Payment failed', "Payment failed for {$order->order_number}; reserved stock was released."],
            'cancelled' => ['Order cancelled', "{$order->order_number} was cancelled."],
            'refunded' => ['Order refunded', "A refund was recorded for {$order->order_number}."],
            'delivered' => ['Order delivered', "{$order->order_number} was marked delivered."],
            'shipped' => ['Order shipped', "{$order->order_number} was marked shipped."],
            default => ['Order updated', "{$order->order_number} was updated."],
        };

        $this->staff->send(new AdminAlertNotification(
            $copy[0],
            $copy[1],
            route('admin.orders.show', $order),
            'order',
        ), ['administrator', 'manager', 'fulfillment', 'support']);
    }
}
