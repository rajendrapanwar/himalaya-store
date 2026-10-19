<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\ServiceablePincode;
use App\Models\User;
use App\Support\Money;

class PricingService
{
    public function __construct(private readonly CouponService $coupons) {}

    /** @return array<string, mixed> */
    public function quote(Cart $cart, ?Coupon $coupon = null, ?ServiceablePincode $destination = null, int $requestedPoints = 0, ?User $user = null): array
    {
        $cart->loadMissing(['items.product', 'bundleItems.bundle.products']);
        $productLines = $cart->items->map(function ($item) {
            $pricePaise = Money::toPaise($item->product->sale_price ?? $item->product->price);

            return ['kind' => 'product', 'model' => $item->product, 'quantity' => $item->quantity, 'price' => Money::toRupees($pricePaise), 'total' => Money::toRupees($pricePaise * $item->quantity), 'total_paise' => $pricePaise * $item->quantity];
        });
        $bundleLines = $cart->bundleItems->map(function ($item) {
            $pricePaise = Money::toPaise($item->bundle->price);

            return ['kind' => 'bundle', 'model' => $item->bundle, 'quantity' => $item->quantity, 'price' => Money::toRupees($pricePaise), 'total' => Money::toRupees($pricePaise * $item->quantity), 'total_paise' => $pricePaise * $item->quantity];
        });
        $lines = $productLines->concat($bundleLines);
        $subtotalPaise = (int) $lines->sum('total_paise');
        $subtotal = Money::toRupees($subtotalPaise);

        $discount = 0.0;
        if ($coupon) {
            $this->coupons->validateForCart($coupon, $subtotal, $user, $cart);
            $eligiblePaise = Money::toPaise($this->coupons->eligibleSubtotal($coupon, $cart));
            $discountPaise = $coupon->type === 'percent'
                ? Money::percentage($eligiblePaise, (int) round((float) $coupon->value * 100))
                : Money::toPaise($coupon->value);
            if ($coupon->max_discount_amount !== null) {
                $discountPaise = min($discountPaise, Money::toPaise($coupon->max_discount_amount));
            }
            $discount = Money::toRupees(min($discountPaise, $eligiblePaise));
        }

        $points = 0;
        $loyaltyDiscount = 0.0;
        if ($user && $requestedPoints > 0) {
            $ratePaise = max(1, Money::toPaise(config('commerce.loyalty.rupees_per_point')));
            $discountPaise = Money::toPaise($discount);
            $maxDiscountPaise = intdiv(max(0, $subtotalPaise - $discountPaise) * (int) config('commerce.loyalty.maximum_redemption_percent'), 100);
            $maxPoints = intdiv($maxDiscountPaise, $ratePaise);
            $points = (int) min($requestedPoints, (int) $user->loyalty_points, $maxPoints);
            $loyaltyDiscount = Money::toRupees($points * $ratePaise);
        }

        // Free delivery is judged on the order value after coupons but before loyalty
        // points, so redeeming points can never add a delivery charge that costs the
        // customer more than the points saved.
        $qualifyingPaise = max(0, $subtotalPaise - Money::toPaise($discount));
        $merchandiseTotalPaise = max(0, $qualifyingPaise - Money::toPaise($loyaltyDiscount));
        $freeShippingThreshold = (float) config('commerce.free_shipping_threshold', 500);
        $freeShippingThresholdPaise = Money::toPaise($freeShippingThreshold);
        $thresholdFreeShipping = $freeShippingThresholdPaise > 0 && $qualifyingPaise > $freeShippingThresholdPaise;
        $freeShippingEligible = (bool) $coupon?->free_shipping || $thresholdFreeShipping;
        $amountUntilFreeShipping = $freeShippingEligible || $freeShippingThresholdPaise <= 0
            ? 0.0
            : Money::toRupees(($freeShippingThresholdPaise - $qualifyingPaise) + 1);
        $shippingPaise = $destination && ! $freeShippingEligible ? Money::toPaise($destination->shipping_amount) : 0;
        $shipping = Money::toRupees($shippingPaise);
        $total = Money::toRupees($merchandiseTotalPaise + $shippingPaise);

        return compact('lines', 'subtotal', 'discount', 'shipping', 'points', 'loyaltyDiscount', 'total', 'freeShippingEligible', 'freeShippingThreshold', 'amountUntilFreeShipping');
    }

    /** @return array<int, int> */
    public function inventoryAllocations(Cart $cart): array
    {
        $cart->loadMissing(['items.product', 'bundleItems.bundle.products']);
        $allocations = [];
        foreach ($cart->items as $item) {
            $allocations[$item->product_id] = ($allocations[$item->product_id] ?? 0) + $item->quantity;
        }
        foreach ($cart->bundleItems as $bundleItem) {
            foreach ($bundleItem->bundle->products as $product) {
                $quantity = $bundleItem->quantity * max(1, (int) $product->pivot->quantity);
                $allocations[$product->id] = ($allocations[$product->id] ?? 0) + $quantity;
            }
        }

        return $allocations;
    }
}
