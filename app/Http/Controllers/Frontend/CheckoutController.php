<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServiceablePincode;
use App\Models\StripeWebhookEvent;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\InventoryService;
use App\Services\LoyaltyService;
use App\Services\OrderLifecycleService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Stripe\Charge;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $carts,
        private readonly PricingService $pricing,
        private readonly CouponService $coupons,
        private readonly InventoryService $inventory,
        private readonly LoyaltyService $loyalty,
        private readonly OrderLifecycleService $orders,
    ) {}

    public function index(Request $request)
    {
        $cart = $this->carts->current($request->user(), false);
        if (! $cart || ($cart->items()->count() + $cart->bundleItems()->count()) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $cart = $this->carts->load($cart);
        $coupon = $this->coupons->find(session('coupon_code'));
        $savedAddress = Address::where('user_id', $request->user()->id)->latest()->first();
        $defaultPincode = $savedAddress?->pincode ?: session('delivery_pincode');
        $destination = $defaultPincode
            ? ServiceablePincode::where('pincode', $defaultPincode)->where('is_active', true)->first()
            : null;
        try {
            $quote = $this->pricing->quote($cart, $coupon, $destination, 0, $request->user());
        } catch (ValidationException) {
            session()->forget('coupon_code');
            $coupon = null;
            $quote = $this->pricing->quote($cart, null, $destination, 0, $request->user());
        }

        $nameParts = preg_split('/\s+/', trim($request->user()->name), 2);
        $checkoutDefaults = [
            'first_name' => $savedAddress?->first_name ?: ($nameParts[0] ?? ''),
            'last_name' => $savedAddress?->last_name ?: ($nameParts[1] ?? ''),
            'email' => $savedAddress?->email ?: $request->user()->email,
            'phone' => $savedAddress?->phone ?: '',
            'address_line_1' => $savedAddress?->address_line_1 ?: '',
            'address_line_2' => $savedAddress?->address_line_2 ?: '',
            'city' => $savedAddress?->city ?: '',
            'state' => $savedAddress?->state ?: 'Uttarakhand',
            'pincode' => $defaultPincode ?: '',
        ];

        return view('frontend.checkout.index', [
            'cart' => $cart,
            'coupon' => $coupon,
            'subtotal' => $quote['subtotal'],
            'discountAmount' => $quote['discount'],
            'shippingAmount' => $quote['shipping'],
            'loyaltyDiscount' => $quote['loyaltyDiscount'],
            'total' => $quote['total'],
            'destination' => $destination,
            'checkoutDefaults' => $checkoutDefaults,
        ]);
    }

    public function quote(Request $request)
    {
        $data = $request->validate([
            'pincode' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'loyalty_points' => ['nullable', 'integer', 'min:0'],
        ]);
        $destination = ServiceablePincode::where('pincode', $data['pincode'])->where('is_active', true)->first();
        if (! $destination) {
            throw ValidationException::withMessages(['pincode' => 'Delivery is not currently available for this pincode.']);
        }

        $cart = $this->carts->current($request->user(), false);
        if (! $cart || ($cart->items()->count() + $cart->bundleItems()->count()) === 0) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        $cart = $this->carts->load($cart);
        $coupon = $this->coupons->find(session('coupon_code'));
        try {
            $quote = $this->pricing->quote($cart, $coupon, $destination, (int) ($data['loyalty_points'] ?? 0), $request->user());
        } catch (ValidationException) {
            session()->forget('coupon_code');
            $quote = $this->pricing->quote($cart, null, $destination, (int) ($data['loyalty_points'] ?? 0), $request->user());
        }

        return response()->json([
            'subtotal' => $quote['subtotal'],
            'discount' => $quote['discount'],
            'shipping' => $quote['shipping'],
            'points' => $quote['points'],
            'loyalty_discount' => $quote['loyaltyDiscount'],
            'total' => $quote['total'],
            'location' => "{$destination->city}, {$destination->state}",
            'estimate' => "{$destination->estimated_days_min}–{$destination->estimated_days_max} business days",
            'cod_available' => $destination->cod_available,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'customer_note' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,stripe',
            'loyalty_points' => 'nullable|integer|min:0',
        ]);

        $destination = ServiceablePincode::where('pincode', $data['pincode'])->where('is_active', true)->first();
        if (! $destination) {
            throw ValidationException::withMessages(['pincode' => 'Delivery is not currently available for this pincode.']);
        }
        if ($data['payment_method'] === 'cod' && ! $destination->cod_available) {
            throw ValidationException::withMessages(['payment_method' => 'Cash on delivery is not available for this pincode.']);
        }

        $cart = $this->carts->current($request->user(), false);
        if (! $cart || ($cart->items()->count() + $cart->bundleItems()->count()) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $order = DB::transaction(function () use ($request, $data, $destination, $cart) {
            $cart = $this->carts->load($cart->newQuery()->lockForUpdate()->findOrFail($cart->id));
            $coupon = $this->coupons->find(session('coupon_code'));
            $quote = $this->pricing->quote($cart, $coupon, $destination, (int) ($data['loyalty_points'] ?? 0), $request->user());
            $expiresAt = $data['payment_method'] === 'stripe' ? now()->addMinutes((int) config('commerce.stripe_reservation_minutes')) : null;

            $address = Address::create([
                'user_id' => $request->user()->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'] ?? null,
                'city' => $data['city'],
                'state' => $data['state'],
                'pincode' => $data['pincode'],
                'country' => 'India',
            ]);

            $order = Order::create([
                'order_number' => 'HS-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(4))),
                'user_id' => $request->user()->id,
                'address_id' => $address->id,
                'coupon_id' => $coupon?->id,
                'subtotal' => $quote['subtotal'],
                'coupon_code' => $coupon?->code,
                'discount_amount' => $quote['discount'],
                'loyalty_points_redeemed' => $quote['points'],
                'loyalty_discount_amount' => $quote['loyaltyDiscount'],
                'shipping_amount' => $quote['shipping'],
                'total_amount' => $quote['total'],
                'currency' => config('commerce.currency'),
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'payment_expires_at' => $expiresAt,
                'order_status' => 'pending',
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($quote['lines'] as $line) {
                $isBundle = $line['kind'] === 'bundle';
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $isBundle ? null : $line['model']->id,
                    'bundle_id' => $isBundle ? $line['model']->id : null,
                    'product_name' => $line['model']->name,
                    'product_sku' => $isBundle ? 'BUNDLE-'.$line['model']->id : $line['model']->sku,
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                    'total' => $line['total'],
                ]);
            }

            $this->inventory->reserve($order, $this->pricing->inventoryAllocations($cart), $expiresAt);
            if ($coupon) {
                $this->coupons->reserve($coupon, $order, $quote['discount']);
            }
            if ($quote['points'] > 0) {
                $this->loyalty->redeem($request->user(), $order, $quote['points']);
            }

            $cart->items()->delete();
            $cart->bundleItems()->delete();
            $cart->delete();
            session()->forget('coupon_code');

            return $order;
        });

        if ($order->payment_method === 'cod') {
            $this->orders->commitCod($order);

            return redirect()->route('checkout.success', $order);
        }

        return $this->createStripeCheckout($order);
    }

    public function success(Order $order, Request $request)
    {
        Gate::authorize('view', $order);
        if ($order->payment_method === 'stripe' && $request->filled('session_id')) {
            $this->syncStripePayment($order, $request->string('session_id')->toString());
        }

        return view('frontend.checkout.success', ['order' => $order->fresh()->load(['items', 'address'])]);
    }

    public function stripeCheckout(Order $order, Request $request)
    {
        Gate::authorize('view', $order);
        abort_unless($order->payment_method === 'stripe' && $order->payment_status === 'pending' && ! $order->cancelled_at, 422);
        abort_if($order->payment_expires_at?->isPast(), 422, 'This payment session has expired.');

        return $this->createStripeCheckout($order);
    }

    public function stripeWebhook(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');
        abort_unless($secret, 503, 'Stripe webhook secret is not configured.');
        try {
            $event = Webhook::constructEvent($request->getContent(), $request->header('Stripe-Signature'), $secret);
        } catch (\UnexpectedValueException|SignatureVerificationException) {
            return response('Invalid webhook signature.', 400);
        }

        $record = StripeWebhookEvent::firstOrCreate(
            ['stripe_event_id' => $event->id],
            ['type' => $event->type, 'payload' => json_decode($request->getContent(), true), 'status' => 'processing'],
        );
        if (! $record->wasRecentlyCreated && $record->status === 'processed') {
            return response('ok');
        }

        try {
            $session = $event->data->object;
            if (in_array($event->type, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true) && $session instanceof StripeCheckoutSession) {
                $order = Order::where('stripe_checkout_session_id', $session->id)->first();
                if ($order && $session->payment_status === 'paid') {
                    $this->assertStripeAmount($order, $session);
                    $this->orders->markPaid($order, is_string($session->payment_intent) ? $session->payment_intent : null);
                }
            } elseif (in_array($event->type, ['checkout.session.async_payment_failed', 'checkout.session.expired'], true) && $session instanceof StripeCheckoutSession) {
                $order = Order::where('stripe_checkout_session_id', $session->id)->first();
                if ($order) {
                    $this->orders->failPayment($order, $event->type);
                }
            } elseif ($event->type === 'charge.refunded' && $session instanceof Charge && is_string($session->payment_intent)) {
                $order = Order::where('stripe_payment_intent_id', $session->payment_intent)->first();
                if ($order) {
                    $this->orders->cancel($order, null, 'Stripe refund');
                    $order->update(['payment_status' => 'refunded']);
                }
            }
            $record->update(['status' => 'processed', 'processed_at' => now(), 'error' => null]);
        } catch (\Throwable $exception) {
            report($exception);
            $record->update(['status' => 'failed', 'error' => $exception->getMessage()]);

            return response('Webhook processing failed.', 500);
        }

        return response('ok');
    }

    private function createStripeCheckout(Order $order)
    {
        $secret = config('services.stripe.secret');
        if (! $secret || str_contains($secret, 'replace_with')) {
            return redirect()->route('orders.show', $order)->with('error', 'Stripe is not configured. Add a valid test secret to .env.');
        }

        try {
            $order->load(['address']);
            $session = (new StripeClient($secret))->checkout->sessions->create([
                'mode' => 'payment',
                'payment_method_types' => ['card', 'upi'],
                'customer_email' => $order->address->email,
                'client_reference_id' => (string) $order->id,
                'metadata' => ['order_id' => (string) $order->id, 'order_number' => $order->order_number],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($order->currency),
                        'product_data' => ['name' => 'Pahadi Roots order '.$order->order_number],
                        'unit_amount' => (int) round((float) $order->total_amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'expires_at' => $order->payment_expires_at?->timestamp,
                'success_url' => route('checkout.success', $order).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('orders.show', $order),
            ], ['idempotency_key' => "order-{$order->id}-checkout"]);
            $order->update(['stripe_checkout_session_id' => $session->id]);

            return redirect()->away($session->url);
        } catch (ApiErrorException $exception) {
            report($exception);

            return redirect()->route('orders.show', $order)->with('error', 'We could not start Stripe Checkout. You can retry from this order.');
        }
    }

    private function syncStripePayment(Order $order, string $sessionId): void
    {
        if ($sessionId !== $order->stripe_checkout_session_id || $order->payment_status === 'paid') {
            return;
        }
        $secret = config('services.stripe.secret');
        if (! $secret || str_contains($secret, 'replace_with')) {
            return;
        }
        try {
            $session = (new StripeClient($secret))->checkout->sessions->retrieve($sessionId);
            if ($session->payment_status === 'paid') {
                $this->assertStripeAmount($order, $session);
                $this->orders->markPaid($order, is_string($session->payment_intent) ? $session->payment_intent : null);
            }
        } catch (ApiErrorException $exception) {
            report($exception);
        }
    }

    private function assertStripeAmount(Order $order, StripeCheckoutSession $session): void
    {
        $expected = (int) round((float) $order->total_amount * 100);
        if ((int) $session->amount_total !== $expected || strtolower((string) $session->currency) !== strtolower($order->currency)) {
            throw new \RuntimeException('Stripe amount or currency does not match the order.');
        }
    }
}
