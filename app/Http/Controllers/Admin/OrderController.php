<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderLifecycleChanged;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\OrderLifecycleService;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Throwable;

class OrderController extends Controller
{
    public function __construct(private readonly OrderLifecycleService $lifecycle) {}

    public function index(Request $request)
    {
        $orders = Order::with('address')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('address', fn ($address) => $address->where('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, fn ($query, $status) => $query->where('order_status', $status))
            ->when($request->payment, fn ($query, $payment) => $query->where('payment_status', $payment))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load(['items.product', 'items.bundle', 'address', 'user', 'statusHistory.user'])]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'courier_name' => 'nullable|required_if:order_status,shipped|string|max:100',
            'tracking_number' => 'nullable|required_if:order_status,shipped|string|max:255',
        ]);

        $transitions = [
            'pending' => ['pending', 'processing', 'cancelled'],
            'processing' => ['processing', 'shipped', 'delivered', 'cancelled'],
            'shipped' => ['shipped', 'delivered', 'cancelled'],
            'delivered' => ['delivered'],
            'cancelled' => ['cancelled'],
        ];
        abort_unless(in_array($data['order_status'], $transitions[$order->order_status] ?? [], true), 422, 'That order status transition is not allowed.');

        $order->update(['courier_name' => $data['courier_name'] ?? null, 'tracking_number' => $data['tracking_number'] ?? null]);
        if ($data['order_status'] === 'cancelled') {
            $this->lifecycle->cancel($order, $request->user()->id);
        } elseif ($data['order_status'] === 'delivered') {
            $this->lifecycle->markDelivered($order, $request->user()->id);
        } elseif ($data['order_status'] !== $order->order_status) {
            $from = $order->order_status;
            $updates = ['order_status' => $data['order_status']];
            if ($data['order_status'] === 'shipped') {
                $updates['shipped_at'] = now();
            }
            $order->update($updates);
            OrderStatusHistory::create(['order_id' => $order->id, 'user_id' => $request->user()->id, 'from_status' => $from, 'to_status' => $data['order_status'], 'source' => 'admin']);
            if ($data['order_status'] === 'shipped') {
                OrderLifecycleChanged::dispatch($order->id, 'shipped');
            }
        }

        if ($order->payment_method === 'cod' && ($data['payment_status'] ?? null) === 'paid' && $order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Order updated successfully.');
    }

    public function refund(Request $request, Order $order)
    {
        abort_unless($order->payment_status === 'paid', 422, 'Only paid orders can be refunded.');
        if ($order->payment_method === 'stripe') {
            abort_unless(filled($order->stripe_payment_intent_id), 422, 'Stripe payment intent is missing.');
            try {
                (new StripeClient(config('services.stripe.secret')))->refunds->create(
                    ['payment_intent' => $order->stripe_payment_intent_id, 'metadata' => ['order_id' => (string) $order->id]],
                    ['idempotency_key' => "order-{$order->id}-refund"],
                );
            } catch (Throwable $exception) {
                report($exception);

                return back()->with('error', 'Stripe could not process the refund. No local order status was changed; verify the payment in Stripe and try again.');
            }
        }

        $this->lifecycle->cancel($order, $request->user()->id, 'Refunded by administrator');
        $order->update(['payment_status' => 'refunded']);

        return back()->with('success', 'Refund recorded and order cancelled.');
    }
}
