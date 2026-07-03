<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function getCart()
    {
        return Cart::with(['items.product.images'])
            ->where('session_id', session()->getId())
            ->first();
    }

    public function index()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        return view('frontend.checkout.index', compact('cart'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'required|string|max:20',
            'address_line_1'  => 'required|string|max:255',
            'address_line_2'  => 'nullable|string|max:255',
            'city'            => 'required|string|max:255',
            'state'           => 'required|string|max:255',
            'pincode'         => 'required|string|max:20',
            'customer_note'   => 'nullable|string',
            'payment_method'  => 'required|in:cod',
        ]);

        $cart = $this->getCart();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        return DB::transaction(function () use ($request, $cart) {

            $subtotal = $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $shippingAmount = 0;
            $totalAmount = $subtotal + $shippingAmount;

            $address = Address::create([
                'user_id'        => auth()->id(),
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city'           => $request->city,
                'state'          => $request->state,
                'pincode'        => $request->pincode,
                'country'        => 'India',
            ]);

            $order = Order::create([
                'order_number'    => 'HS-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'user_id'         => auth()->id(),
                'address_id'      => $address->id,
                'subtotal'        => $subtotal,
                'shipping_amount' => $shippingAmount,
                'total_amount'    => $totalAmount,
                'payment_method'  => $request->payment_method,
                'payment_status'  => 'pending',
                'order_status'    => 'pending',
                'customer_note'   => $request->customer_note,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'quantity'     => $item->quantity,
                    'price'        => $item->price,
                    'total'        => $item->price * $item->quantity,
                ]);

                $product->decrement('stock_quantity', $item->quantity);
            }

            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('checkout.success', $order);
        });
    }

    public function success(Order $order)
    {
        $order->load(['items', 'address']);

        return view('frontend.checkout.success', compact('order'));
    }
}
