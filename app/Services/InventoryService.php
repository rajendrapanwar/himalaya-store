<?php

namespace App\Services;

use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /** @param array<int, int> $allocations */
    public function reserve(Order $order, array $allocations, ?\DateTimeInterface $expiresAt): void
    {
        foreach ($allocations as $productId => $quantity) {
            $product = Product::lockForUpdate()->findOrFail($productId);
            if (! $product->status || $product->available_stock < $quantity) {
                throw ValidationException::withMessages([
                    'cart' => "{$product->name} no longer has enough available stock.",
                ]);
            }

            $product->increment('reserved_quantity', $quantity);
            InventoryReservation::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'status' => 'reserved',
                'expires_at' => $expiresAt,
            ]);
        }

        $order->update(['inventory_reserved_at' => now()]);
    }

    public function commit(Order $order): void
    {
        if ($order->inventory_committed_at) {
            return;
        }

        $reservations = InventoryReservation::where('order_id', $order->id)->lockForUpdate()->get();
        foreach ($reservations->where('status', 'reserved') as $reservation) {
            $product = Product::lockForUpdate()->findOrFail($reservation->product_id);
            if ($product->stock_quantity < $reservation->quantity || $product->reserved_quantity < $reservation->quantity) {
                throw new \RuntimeException("Inventory reservation for product {$product->id} is inconsistent.");
            }

            $product->decrement('stock_quantity', $reservation->quantity);
            $product->decrement('reserved_quantity', $reservation->quantity);
            $reservation->update(['status' => 'committed', 'committed_at' => now()]);
        }

        $order->update(['inventory_committed_at' => now()]);
    }

    public function release(Order $order, bool $restockCommitted = false): void
    {
        $reservations = InventoryReservation::where('order_id', $order->id)->lockForUpdate()->get();
        foreach ($reservations as $reservation) {
            if ($reservation->status === 'reserved') {
                $product = Product::lockForUpdate()->findOrFail($reservation->product_id);
                $product->decrement('reserved_quantity', min($product->reserved_quantity, $reservation->quantity));
                $reservation->update(['status' => 'released', 'released_at' => now()]);
            } elseif ($restockCommitted && $reservation->status === 'committed') {
                Product::lockForUpdate()->findOrFail($reservation->product_id)->increment('stock_quantity', $reservation->quantity);
                $reservation->update(['status' => 'restocked', 'released_at' => now()]);
            }
        }
    }
}
