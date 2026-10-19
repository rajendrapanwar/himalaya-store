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
        $sessionCartId = session()->get('cart_id');
        $sessionCart = $sessionCartId
            ? Cart::query()
                ->whereKey($sessionCartId)
                ->where(function ($query) use ($user) {
                    $query->whereNull('user_id');
                    if ($user) {
                        $query->orWhere('user_id', $user->id);
                    }
                })
                ->first()
            : null;
        $sessionCart ??= Cart::where('session_id', $sessionId)->latest('id')->first();
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

        if ($cart) {
            session()->put('cart_id', $cart->id);
        }

        return $cart;
    }

    /**
     * Attach a cart created before authentication to the signed-in customer.
     *
     * Authentication rotates the session ID, so the previous ID must be used
     * to find the guest cart before assigning the new, secure session ID.
     */
    public function transferGuestCart(string $previousSessionId, User $user, ?int $guestCartId = null): ?Cart
    {
        $guestCartId ??= session()->get('cart_id');
        $guestCart = $guestCartId
            ? Cart::query()->whereKey($guestCartId)->whereNull('user_id')->first()
            : null;
        $guestCart ??= Cart::where('session_id', $previousSessionId)->whereNull('user_id')->latest('id')->first();
        $userCart = Cart::where('user_id', $user->id)->latest('updated_at')->first();

        if ($guestCart && $userCart && ! $guestCart->is($userCart)) {
            $userCart = $this->merge($guestCart, $userCart);
        }

        $cart = $userCart ?: $guestCart;

        if ($cart) {
            $cart->update([
                'session_id' => session()->getId(),
                'user_id' => $user->id,
            ]);
            session()->put('cart_id', $cart->id);
        }

        return $cart?->fresh();
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
