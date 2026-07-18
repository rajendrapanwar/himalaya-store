<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartBundleItem;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function current(?User $user = null, bool $create = true): ?Cart
    {
        $sessionId = session()->getId();
        $sessionCart = Cart::where('session_id', $sessionId)->latest('id')->first();
        $userCart = $user ? Cart::where('user_id', $user->id)->latest('updated_at')->first() : null;

        if ($user && $sessionCart && $userCart && ! $sessionCart->is($userCart)) {
            $userCart = $this->merge($sessionCart, $userCart);
        }

        $cart = $userCart ?: $sessionCart;
        if (! $cart && $create) {
            $cart = Cart::create(['session_id' => $sessionId, 'user_id' => $user?->id]);
        }

        if ($cart && ($cart->session_id !== $sessionId || ($user && $cart->user_id !== $user->id))) {
            $cart->update(['session_id' => $sessionId, 'user_id' => $user?->id ?: $cart->user_id]);
        }

        return $cart;
    }

    public function load(Cart $cart): Cart
    {
        return $cart->load(['items.product.images', 'bundleItems.bundle.products.images']);
    }

    private function merge(Cart $source, Cart $target): Cart
    {
        return DB::transaction(function () use ($source, $target) {
            $source = Cart::with(['items.product', 'bundleItems.bundle.products'])->lockForUpdate()->findOrFail($source->id);
            $target = Cart::with(['items.product', 'bundleItems.bundle.products'])->lockForUpdate()->findOrFail($target->id);

            foreach ($source->items as $item) {
                $existing = CartItem::where('cart_id', $target->id)->where('product_id', $item->product_id)->lockForUpdate()->first();
                $available = $item->product && $item->product->status ? $item->product->available_stock : 0;
                if ($available < 1) {
                    $item->delete();

                    continue;
                }
                if ($existing) {
                    $existing->update(['quantity' => min($available, $existing->quantity + $item->quantity)]);
                } else {
                    $item->update(['cart_id' => $target->id, 'quantity' => min($available, $item->quantity)]);
                }
            }

            foreach ($source->bundleItems as $item) {
                $existing = CartBundleItem::where('cart_id', $target->id)->where('bundle_id', $item->bundle_id)->lockForUpdate()->first();
                $available = $item->bundle && $item->bundle->is_active ? $item->bundle->available_stock : 0;
                if ($available < 1) {
                    $item->delete();

                    continue;
                }
                if ($existing) {
                    $existing->update(['quantity' => min($available, $existing->quantity + $item->quantity)]);
                } else {
                    $item->update(['cart_id' => $target->id, 'quantity' => min($available, $item->quantity)]);
                }
            }

            $source->delete();

            return $target->fresh();
        });
    }
}
