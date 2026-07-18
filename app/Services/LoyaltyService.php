<?php

namespace App\Services;

use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoyaltyService
{
    public function credit(User $user, int $points, string $type, string $description, string $reference, ?Order $order = null): void
    {
        if ($points <= 0 || LoyaltyTransaction::where('reference', $reference)->exists()) {
            return;
        }

        $user = User::lockForUpdate()->findOrFail($user->id);
        $user->increment('loyalty_points', $points);
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'order_id' => $order?->id,
            'type' => $type,
            'points' => $points,
            'balance_after' => $user->fresh()->loyalty_points,
            'reference' => $reference,
            'description' => $description,
        ]);
    }

    public function redeem(User $user, Order $order, int $points): void
    {
        if ($points <= 0) {
            return;
        }

        $user = User::lockForUpdate()->findOrFail($user->id);
        if ($user->loyalty_points < $points) {
            throw ValidationException::withMessages(['loyalty_points' => 'Your loyalty point balance is not sufficient.']);
        }

        $user->decrement('loyalty_points', $points);
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => 'redemption',
            'points' => -$points,
            'balance_after' => $user->fresh()->loyalty_points,
            'reference' => "order:{$order->id}:redemption",
            'description' => "Redeemed on order {$order->order_number}",
        ]);
    }

    public function awardPurchase(Order $order): void
    {
        $points = (int) floor((float) $order->total_amount * (float) config('commerce.loyalty.points_per_rupee'));
        $this->credit($order->user, $points, 'purchase_reward', "Points earned from {$order->order_number}", "order:{$order->id}:purchase", $order);
    }

    public function debit(User $user, int $points, string $type, string $description, string $reference, ?Order $order = null): void
    {
        if ($points <= 0 || LoyaltyTransaction::where('reference', $reference)->exists()) {
            return;
        }
        $user = User::lockForUpdate()->findOrFail($user->id);
        $user->decrement('loyalty_points', $points);
        LoyaltyTransaction::create(['user_id' => $user->id, 'order_id' => $order?->id, 'type' => $type, 'points' => -$points, 'balance_after' => $user->fresh()->loyalty_points, 'reference' => $reference, 'description' => $description]);
    }

    public function reverseRedemption(Order $order): void
    {
        $transaction = LoyaltyTransaction::where('reference', "order:{$order->id}:redemption")->first();
        if (! $transaction || LoyaltyTransaction::where('reference', "order:{$order->id}:redemption-reversal")->exists()) {
            return;
        }

        $this->credit($order->user, abs($transaction->points), 'redemption_reversal', "Points returned for {$order->order_number}", "order:{$order->id}:redemption-reversal", $order);
    }

    public function awardReferral(Order $order): void
    {
        $order = Order::with('user.referrer')->lockForUpdate()->findOrFail($order->id);
        if ($order->referral_rewarded_at || ! $order->user?->referrer) {
            return;
        }

        $hasEarlierQualifiedOrder = Order::where('user_id', $order->user_id)
            ->whereKeyNot($order->id)
            ->where(function ($query) {
                $query->whereNotNull('paid_at')->orWhereNotNull('delivered_at');
            })->exists();

        if ($hasEarlierQualifiedOrder) {
            $order->update(['referral_rewarded_at' => now()]);

            return;
        }

        $this->credit($order->user->referrer, (int) config('commerce.loyalty.referrer_reward'), 'referral_reward', "Referral reward for {$order->order_number}", "order:{$order->id}:referrer", $order);
        $this->credit($order->user, (int) config('commerce.loyalty.referred_customer_reward'), 'referral_welcome', "Welcome reward for {$order->order_number}", "order:{$order->id}:referred", $order);
        $order->update(['referral_rewarded_at' => now()]);
    }

    public function reverseOrderRewards(Order $order): void
    {
        $order->loadMissing('user.referrer');
        $purchase = LoyaltyTransaction::where('reference', "order:{$order->id}:purchase")->first();
        if ($purchase) {
            $this->debit($order->user, $purchase->points, 'purchase_reversal', "Purchase points reversed for {$order->order_number}", "order:{$order->id}:purchase-reversal", $order);
        }
        $referred = LoyaltyTransaction::where('reference', "order:{$order->id}:referred")->first();
        if ($referred) {
            $this->debit($order->user, $referred->points, 'referral_reversal', "Referral reward reversed for {$order->order_number}", "order:{$order->id}:referred-reversal", $order);
        }
        $referrer = LoyaltyTransaction::where('reference', "order:{$order->id}:referrer")->first();
        if ($referrer && $order->user?->referrer) {
            $this->debit($order->user->referrer, $referrer->points, 'referral_reversal', "Referral reward reversed for {$order->order_number}", "order:{$order->id}:referrer-reversal", $order);
        }
    }
}
