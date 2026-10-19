<?php

namespace App\Services;

use App\Events\OrderLifecycleChanged;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;

class OrderLifecycleService
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly CouponService $coupons,
        private readonly LoyaltyService $loyalty,
    ) {}

    public function markPaid(Order $order, ?string $paymentIntentId = null): Order
    {
        $wasPaid = $order->payment_status === 'paid';
        $result = DB::transaction(function () use ($order, $paymentIntentId) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            if ($order->payment_status === 'paid') {
                return $order;
            }

            $this->inventory->commit($order);
            $this->coupons->redeem($order);
            $from = $order->order_status;
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'stripe_payment_intent_id' => $paymentIntentId ?: $order->stripe_payment_intent_id,
                'order_status' => $from === 'pending' ? 'processing' : $from,
            ]);
            $this->history($order, $from, $order->order_status, 'payment');
            $this->loyalty->awardPurchase($order);
            $this->loyalty->awardReferral($order);

            return $order->fresh();
        });
        if (! $wasPaid && $result->payment_status === 'paid') {
            OrderLifecycleChanged::dispatch($result->id, 'paid');
        }

        return $result;
    }

    public function commitCod(Order $order): Order
    {
        $result = DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            $this->inventory->commit($order);
            $this->coupons->redeem($order);

            return $order->fresh();
        });
        OrderLifecycleChanged::dispatch($result->id, 'placed');

        return $result;
    }

    public function failPayment(Order $order, string $reason = 'Payment failed'): Order
    {
        $wasTerminal = in_array($order->payment_status, ['failed', 'paid', 'refunded'], true);
        $result = DB::transaction(function () use ($order, $reason) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            if (in_array($order->payment_status, ['paid', 'refunded'], true)) {
                return $order;
            }
            $this->inventory->release($order);
            $this->coupons->release($order);
            $this->loyalty->reverseRedemption($order);
            $this->loyalty->reverseOrderRewards($order);
            $from = $order->order_status;
            $order->update(['payment_status' => 'failed', 'order_status' => 'cancelled', 'cancelled_at' => now()]);
            $this->history($order, $from, 'cancelled', 'payment', $reason);

            return $order->fresh();
        });
        if (! $wasTerminal && $result->payment_status === 'failed') {
            OrderLifecycleChanged::dispatch($result->id, 'payment_failed');
        }

        return $result;
    }

    public function cancel(Order $order, ?int $actorId = null, string $reason = 'Cancelled by administrator'): Order
    {
        $wasCancelled = $order->order_status === 'cancelled';
        $result = DB::transaction(function () use ($order, $actorId, $reason) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            if ($order->order_status === 'cancelled') {
                return $order;
            }
            $this->inventory->release($order, (bool) $order->inventory_committed_at);
            $this->coupons->release($order);
            $this->loyalty->reverseRedemption($order);
            $this->loyalty->reverseOrderRewards($order);
            $from = $order->order_status;
            $order->update(['order_status' => 'cancelled', 'cancelled_at' => now()]);
            $this->history($order, $from, 'cancelled', 'admin', $reason, $actorId);

            return $order->fresh();
        });
        if (! $wasCancelled && $result->order_status === 'cancelled') {
            $event = str_contains(strtolower($reason), 'refund') ? 'refunded' : 'cancelled';
            OrderLifecycleChanged::dispatch($result->id, $event);
        }

        return $result;
    }

    public function markDelivered(Order $order, ?int $actorId = null): Order
    {
        $wasDelivered = $order->order_status === 'delivered';
        $result = DB::transaction(function () use ($order, $actorId) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            $from = $order->order_status;
            $updates = ['order_status' => 'delivered', 'delivered_at' => now()];
            if ($order->payment_method === 'cod' && $order->payment_status !== 'paid') {
                $updates['payment_status'] = 'paid';
                $updates['paid_at'] = now();
            }
            $order->update($updates);
            $this->history($order, $from, 'delivered', 'admin', null, $actorId);
            $this->loyalty->awardPurchase($order);
            $this->loyalty->awardReferral($order);

            return $order->fresh();
        });
        if (! $wasDelivered && $result->order_status === 'delivered') {
            OrderLifecycleChanged::dispatch($result->id, 'delivered');
        }

        return $result;
    }

    private function history(Order $order, ?string $from, string $to, string $source, ?string $note = null, ?int $userId = null): void
    {
        if ($from === $to && $note === null) {
            return;
        }
        OrderStatusHistory::create(['order_id' => $order->id, 'user_id' => $userId, 'from_status' => $from, 'to_status' => $to, 'source' => $source, 'note' => $note]);
    }
}
