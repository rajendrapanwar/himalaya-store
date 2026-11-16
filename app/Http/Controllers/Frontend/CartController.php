<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\CartBundleItem;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\Analytics;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\MiniCartService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $carts,
        private readonly CouponService $coupons,
        private readonly PricingService $pricing,
    ) {}

    public function index(Request $request)
    {
        $cart = $this->carts->load($this->carts->current($request->user()));
        $coupon = $this->coupons->find(session('coupon_code'));
        try {
            $quote = $this->pricing->quote($cart, $coupon, null, 0, $request->user());
        } catch (ValidationException) {
            session()->forget('coupon_code');
            $coupon = null;
            $quote = $this->pricing->quote($cart, null, null, 0, $request->user());
        }

        return view('frontend.cart.index', [
            'cart' => $cart,
            'subtotal' => $quote['subtotal'],
            'coupon' => $coupon,
            'discountAmount' => $quote['discount'],
            'total' => $quote['total'],
            'freeShippingEligible' => $quote['freeShippingEligible'],
            'freeShippingThreshold' => $quote['freeShippingThreshold'],
            'amountUntilFreeShipping' => $quote['amountUntilFreeShipping'],
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:100']);
        $added = DB::transaction(function () use ($request, $product, $data) {
            $product = Product::lockForUpdate()->findOrFail($product->id);
            if (! $product->status || $product->available_stock < 1) {
                return false;
            }

            $cart = $this->carts->current($request->user());
            $item = CartItem::where(['cart_id' => $cart->id, 'product_id' => $product->id])->lockForUpdate()->first()
                ?? new CartItem(['cart_id' => $cart->id, 'product_id' => $product->id]);
            $requested = ($item->exists ? $item->quantity : 0) + $data['quantity'];
            $item->quantity = min($requested, $product->available_stock);
            $item->price = $product->sale_price ?? $product->price;
            $item->save();

            return true;
        });

        if (! $added) {
            return $request->expectsJson()
                ? response()->json(['added' => false, 'message' => 'This product is currently out of stock.'], 422)
                : back()->with('error', 'This product is currently out of stock.');
        }

        return $request->expectsJson()
            ? $this->addedResponse("{$product->name} was added to your cart.", app(Analytics::class)->productItem($product, (int) $data['quantity']))
            : redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    /**
     * JSON for the storefront's in-place add-to-cart: new badge count plus the re-rendered drawer.
     *
     * @param  array{item_id: string, item_name: string, price: float, quantity: int}  $item
     */
    private function addedResponse(string $message, array $item)
    {
        $miniCart = app(MiniCartService::class)->summary();

        return response()->json([
            'added' => true,
            'message' => $message,
            'cart_count' => $miniCart['count'],
            'drawer' => view('partials.cart-drawer-body', ['miniCart' => $miniCart])->render(),
            // Sent to Google Analytics / Meta as an "add to cart" event when tracking is switched on.
            'analytics' => app(Analytics::class)->enabled()
                ? ['currency' => config('commerce.currency', 'INR'), 'value' => round($item['price'] * $item['quantity'], 2), 'items' => [$item]]
                : null,
        ]);
    }

    public function addBundle(Request $request, Bundle $bundle)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:20']);
        $availability = DB::transaction(function () use ($request, $bundle, $data) {
            $bundle = Bundle::with('products')->lockForUpdate()->findOrFail($bundle->id);
            abort_unless($bundle->is_active, 404);
            $cart = $this->carts->current($request->user());
            $item = CartBundleItem::where(['cart_id' => $cart->id, 'bundle_id' => $bundle->id])->lockForUpdate()->first()
                ?? new CartBundleItem(['cart_id' => $cart->id, 'bundle_id' => $bundle->id]);
            $requested = ($item->exists ? $item->quantity : 0) + $data['quantity'];
            if ($requested > $bundle->available_stock) {
                return ['added' => false, 'stock' => $bundle->available_stock, 'name' => $bundle->name];
            }
            $item->quantity = $requested;
            $item->price = $bundle->price;
            $item->save();

            return ['added' => true];
        });

        if (! $availability['added']) {
            $message = "Only {$availability['stock']} {$availability['name']} bundle(s) are currently available.";

            return $request->expectsJson()
                ? response()->json(['added' => false, 'message' => $message], 422)
                : back()->with('error', $message);
        }

        return $request->expectsJson()
            ? $this->addedResponse("{$bundle->name} was added to your cart.", ['item_id' => 'bundle-'.$bundle->id, 'item_name' => $bundle->name, 'item_category' => 'Gift boxes', 'price' => (float) $bundle->price, 'quantity' => (int) $data['quantity']])
            : redirect()->route('cart.index')->with('success', 'Gift bundle added to cart.');
    }

    /**
     * "Buy again": put everything from a past order back in the cart at today's
     * prices, limited to what is in stock. Items no longer sold are skipped.
     */
    public function buyAgain(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        [$added, $skipped] = DB::transaction(function () use ($request, $order) {
            $cart = $this->carts->current($request->user());
            $added = 0;
            $skipped = 0;

            foreach ($order->items as $orderItem) {
                if ($orderItem->bundle_id) {
                    $bundle = Bundle::with('products')->lockForUpdate()->find($orderItem->bundle_id);
                    $stock = $bundle && $bundle->is_active ? $bundle->available_stock : 0;
                    $item = $stock > 0
                        ? CartBundleItem::where(['cart_id' => $cart->id, 'bundle_id' => $bundle->id])->lockForUpdate()->first() ?? new CartBundleItem(['cart_id' => $cart->id, 'bundle_id' => $bundle->id])
                        : null;
                    $price = $bundle?->price;
                } else {
                    $product = $orderItem->product_id ? Product::lockForUpdate()->find($orderItem->product_id) : null;
                    $stock = $product && $product->status ? $product->available_stock : 0;
                    $item = $stock > 0
                        ? CartItem::where(['cart_id' => $cart->id, 'product_id' => $product->id])->lockForUpdate()->first() ?? new CartItem(['cart_id' => $cart->id, 'product_id' => $product->id])
                        : null;
                    $price = $product ? ($product->sale_price ?? $product->price) : null;
                }

                if (! $item) {
                    $skipped++;

                    continue;
                }
                $item->quantity = min(($item->exists ? $item->quantity : 0) + $orderItem->quantity, $stock);
                $item->price = $price;
                $item->save();
                $added++;
            }

            return [$added, $skipped];
        });

        if ($added === 0) {
            return back()->with('error', 'Sorry, none of the items from this order are available right now.');
        }

        return redirect()->route('cart.index')->with('success', $skipped === 0
            ? 'Everything from that order is back in your cart, at today\'s prices.'
            : "{$added} ".($added === 1 ? 'item is' : 'items are')." back in your cart. {$skipped} could not be added because ".($skipped === 1 ? 'it is' : 'they are').' out of stock or no longer sold.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->cart->belongsToCurrentVisitor(), 403);
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:100']);
        $product = $cartItem->product;
        if (! $product || ! $product->status || $product->available_stock < 1) {
            $cartItem->delete();

            return back()->with('error', 'This product is no longer available and was removed from your cart.');
        }
        $cartItem->update(['quantity' => min($data['quantity'], $product->available_stock), 'price' => $product->sale_price ?? $product->price]);

        return back()->with('success', 'Cart updated.');
    }

    public function updateBundle(Request $request, CartBundleItem $cartBundleItem)
    {
        abort_unless($cartBundleItem->cart->belongsToCurrentVisitor(), 403);
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:20']);
        $bundle = $cartBundleItem->bundle()->with('products')->firstOrFail();
        if (! $bundle->is_active || $data['quantity'] > $bundle->available_stock) {
            return back()->with('error', "Only {$bundle->available_stock} {$bundle->name} bundle(s) are currently available.");
        }
        $cartBundleItem->update(['quantity' => $data['quantity'], 'price' => $bundle->price]);

        return back()->with('success', 'Bundle quantity updated.');
    }

    public function remove(CartItem $cartItem)
    {
        abort_unless($cartItem->cart->belongsToCurrentVisitor(), 403);
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function removeBundle(CartBundleItem $cartBundleItem)
    {
        abort_unless($cartBundleItem->cart->belongsToCurrentVisitor(), 403);
        $cartBundleItem->delete();

        return back()->with('success', 'Bundle removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['code' => 'required|string|max:50']);
        $coupon = $this->coupons->find($data['code']);
        if (! $coupon) {
            return back()->with('error', 'Coupon code was not found.');
        }
        $cart = $this->carts->load($this->carts->current($request->user()));
        $subtotal = $this->pricing->quote($cart, null)['subtotal'];
        try {
            $this->coupons->validateForCart($coupon, $subtotal, $request->user(), $cart);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->validator->errors()->first('coupon'));
        }
        session(['coupon_code' => $coupon->code]);

        return back()->with('success', 'Coupon applied.');
    }

    public function removeCoupon()
    {
        session()->forget('coupon_code');

        return back()->with('success', 'Coupon removed.');
    }
}
