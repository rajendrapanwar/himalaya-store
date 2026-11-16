<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;

/**
 * Google Analytics 4 and Meta Pixel settings for the storefront.
 *
 * Both are optional: nothing is loaded until an ID is set in .env, and staff
 * are never tracked so their browsing does not distort the shop's numbers.
 */
class Analytics
{
    public function ga4Id(): ?string
    {
        $id = (string) config('services.analytics.ga4_id');

        return preg_match('/^G-[A-Z0-9]{4,20}$/', $id) ? $id : null;
    }

    public function metaPixelId(): ?string
    {
        $id = (string) config('services.analytics.meta_pixel_id');

        return preg_match('/^\d{5,20}$/', $id) ? $id : null;
    }

    public function configured(): bool
    {
        return $this->ga4Id() !== null || $this->metaPixelId() !== null;
    }

    public function enabled(): bool
    {
        return $this->configured() && ! auth()->user()?->isStaff();
    }

    /** @return array<string, mixed> */
    public function productItem(Product $product, int $quantity = 1): array
    {
        return [
            'item_id' => $product->sku ?: (string) $product->id,
            'item_name' => $product->name,
            'item_category' => $product->category?->name,
            'price' => (float) ($product->sale_price ?? $product->price),
            'quantity' => $quantity,
        ];
    }

    /**
     * The "purchase" event for a placed order.
     *
     * @return array<string, mixed>
     */
    public function purchase(Order $order): array
    {
        return [
            'transaction_id' => $order->order_number,
            'value' => (float) $order->total_amount,
            'currency' => config('commerce.currency', 'INR'),
            'shipping' => (float) $order->shipping_amount,
            'coupon' => $order->coupon_code,
            'items' => $order->items->map(fn ($item) => [
                'item_id' => $item->product_sku ?: 'item-'.$item->id,
                'item_name' => $item->product_name,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
            ])->values()->all(),
        ];
    }
}
