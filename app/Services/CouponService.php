<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Order;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function find(?string $code): ?Coupon
    {
        if (! $code) {
            return null;
        }

        return Coupon::where('code', strtoupper(trim($code)))->first();
    }

    public function validateForCart(Coupon $coupon, float $subtotal, ?User $user = null, ?Cart $cart = null): void
    {
        if (! $coupon->isAvailableFor($subtotal)) {
            throw ValidationException::withMessages(['coupon' => 'This coupon is invalid or unavailable for this cart.']);
        }

        if ($user) {
            $uses = $coupon->redemptions()->where('user_id', $user->id)->whereIn('status', ['reserved', 'redeemed'])->count();
            if ($uses >= $coupon->per_user_limit) {
                throw ValidationException::withMessages(['coupon' => 'You have reached the usage limit for this coupon.']);
            }
            if ($coupon->first_order_only && $user->orders()->where(fn ($query) => $query->whereNotNull('paid_at')->orWhereNotNull('delivered_at'))->exists()) {
                throw ValidationException::withMessages(['coupon' => 'This coupon is available only for a first order.']);
            }
        }

        if ($cart && $this->eligibleSubtotal($coupon, $cart) <= 0) {
            throw ValidationException::withMessages(['coupon' => 'This coupon does not apply to the products in your cart.']);
        }
    }

    public function eligibleSubtotal(Coupon $coupon, Cart $cart): float
    {
        $coupon->loadMissing(['products:id', 'categories:id']);
        if ($coupon->products->isEmpty() && $coupon->categories->isEmpty()) {
            return (float) $cart->items->sum(fn ($item) => ($item->product->sale_price ?? $item->product->price) * $item->quantity)
                + (float) $cart->bundleItems->sum(fn ($item) => $item->bundle->price * $item->quantity);
        }
        $productIds = $coupon->products->modelKeys();
        $categoryIds = $coupon->categories->modelKeys();

        return round((float) $cart->items->filter(fn ($item) => in_array($item->product_id, $productIds, true) || in_array($item->product->category_id, $categoryIds, true))->sum(fn ($item) => ($item->product->sale_price ?? $item->product->price) * $item->quantity), 2);
    }

    public function reserve(Coupon $coupon, Order $order, float $discount): void
    {
        $coupon = Coupon::lockForUpdate()->findOrFail($coupon->id);
        $this->validateForCart($coupon, (float) $order->subtotal, $order->user);
        $coupon->increment('used_count');
        CouponRedemption::create([
            'coupon_id' => $coupon->id,
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'discount_amount' => $discount,
            'status' => 'reserved',
        ]);
    }

    public function redeem(Order $order): void
    {
        $redemption = CouponRedemption::where('order_id', $order->id)->lockForUpdate()->first();
        if ($redemption && $redemption->status === 'reserved') {
            $redemption->update(['status' => 'redeemed', 'redeemed_at' => now()]);
            $order->update(['coupon_redeemed_at' => now()]);
        }
    }

    public function release(Order $order): void
    {
        $redemption = CouponRedemption::where('order_id', $order->id)->lockForUpdate()->first();
        if (! $redemption || in_array($redemption->status, ['released', 'reversed'], true)) {
            return;
        }

        $coupon = Coupon::lockForUpdate()->find($redemption->coupon_id);
        if ($coupon && $coupon->used_count > 0) {
            $coupon->decrement('used_count');
        }
        $redemption->update([
            'status' => $redemption->status === 'redeemed' ? 'reversed' : 'released',
            'released_at' => now(),
        ]);
    }
}
