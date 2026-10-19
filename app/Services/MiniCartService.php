<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartBundleItem;
use App\Models\CartItem;
use Illuminate\Validation\ValidationException;

/**
 * Builds the read-only cart summary shown in the storefront's slide-out cart
 * drawer. It never creates or merges carts, so it is safe to run on every page.
 */
class MiniCartService
{
    public function __construct(
        private readonly CouponService $coupons,
        private readonly PricingService $pricing,
    ) {}

    /** @return array{count: int, lines: list<array<string, mixed>>, subtotal: float, discount: float, total: float, free_shipping_eligible: bool, free_shipping_threshold: float, amount_until_free_shipping: float, free_shipping_progress: int} */
    public function summary(): array
    {
        $cart = $this->find();
        if (! $cart || ($cart->items->isEmpty() && $cart->bundleItems->isEmpty())) {
            return $this->empty();
        }

        $coupon = $this->coupons->find(session('coupon_code'));
        try {
            $quote = $this->pricing->quote($cart, $coupon, null, 0, auth()->user());
        } catch (ValidationException) {
            $quote = $this->pricing->quote($cart, null, null, 0, auth()->user());
        }

        $lines = $cart->items->map(fn (CartItem $item) => [
            'kind' => 'product',
            'name' => $item->product->name,
            'url' => route('products.show', $item->product->slug),
            'image' => ($image = $item->product->images->firstWhere('is_main', true) ?? $item->product->images->first())
                ? asset('storage/'.$image->image)
                : null,
            'meta' => $item->product->category?->name,
            'quantity' => $item->quantity,
            'line_total' => (float) ($item->product->sale_price ?? $item->product->price) * $item->quantity,
            'remove_url' => route('cart.remove', $item),
        ])->concat($cart->bundleItems->map(fn (CartBundleItem $item) => [
            'kind' => 'bundle',
            'name' => $item->bundle->name,
            'url' => route('bundles.index'),
            'image' => $item->bundle->image ? asset('storage/'.$item->bundle->image) : null,
            'meta' => 'Gift bundle',
            'quantity' => $item->quantity,
            'line_total' => (float) $item->bundle->price * $item->quantity,
            'remove_url' => route('cart.bundles.remove', $item),
        ]))->values()->all();

        $threshold = (float) $quote['freeShippingThreshold'];
        $qualifying = max(0, (float) $quote['subtotal'] - (float) $quote['discount']);

        return [
            'count' => (int) $cart->items->sum('quantity') + (int) $cart->bundleItems->sum('quantity'),
            'lines' => $lines,
            'subtotal' => (float) $quote['subtotal'],
            'discount' => (float) $quote['discount'],
            'total' => (float) $quote['total'],
            'free_shipping_eligible' => (bool) $quote['freeShippingEligible'],
            'free_shipping_threshold' => $threshold,
            'amount_until_free_shipping' => (float) $quote['amountUntilFreeShipping'],
            'free_shipping_progress' => $quote['freeShippingEligible'] || $threshold <= 0 ? 100 : (int) min(100, round($qualifying / $threshold * 100)),
        ];
    }

    private function find(): ?Cart
    {
        if (! session()->isStarted()) {
            return null;
        }

        $relations = ['items.product.images', 'items.product.category', 'bundleItems.bundle.products'];
        $cartId = session('cart_id');
        if ($cartId) {
            $cart = Cart::with($relations)->whereKey($cartId)->where(function ($query) {
                $query->whereNull('user_id');
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })->first();
            if ($cart) {
                return $cart;
            }
        }

        return Cart::with($relations)->where(function ($query) {
            $query->where('session_id', session()->getId());
            if (auth()->check()) {
                $query->orWhere('user_id', auth()->id());
            }
        })->latest('id')->first();
    }

    /** @return array{count: int, lines: list<array<string, mixed>>, subtotal: float, discount: float, total: float, free_shipping_eligible: bool, free_shipping_threshold: float, amount_until_free_shipping: float, free_shipping_progress: int} */
    private function empty(): array
    {
        $threshold = (float) config('commerce.free_shipping_threshold', 500);

        return [
            'count' => 0,
            'lines' => [],
            'subtotal' => 0.0,
            'discount' => 0.0,
            'total' => 0.0,
            'free_shipping_eligible' => false,
            'free_shipping_threshold' => $threshold,
            'amount_until_free_shipping' => $threshold,
            'free_shipping_progress' => 0,
        ];
    }
}
