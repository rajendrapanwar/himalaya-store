<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OperationsController extends Controller
{
    public function customers(Request $request)
    {
        $customers = User::where('role', 'customer')->where('is_admin', false)->withCount('orders')->when($request->search, fn ($query, $search) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('referral_code', 'like', "%{$search}%")))->latest()->paginate(30)->withQueryString();

        return view('admin.operations.customers', compact('customers'));
    }

    public function customer(User $user)
    {
        abort_unless($user->role === 'customer' && ! $user->is_admin, 404);
        $user->loadCount(['orders', 'referrals'])->load(['orders' => fn ($query) => $query->latest()->limit(10), 'loyaltyTransactions' => fn ($query) => $query->latest()->limit(20)]);

        return view('admin.operations.customer', compact('user'));
    }

    public function adjustPoints(Request $request, User $user, LoyaltyService $loyalty)
    {
        abort_unless($user->role === 'customer' && ! $user->is_admin, 404);
        $data = $request->validate(['points' => 'required|integer|not_in:0|min:-100000|max:100000', 'reason' => 'required|string|max:255']);
        $reference = 'admin:'.$request->user()->id.':'.now()->format('YmdHis').':'.$user->id;
        if ($data['points'] > 0) {
            $loyalty->credit($user, $data['points'], 'admin_adjustment', $data['reason'], $reference);
        } else {
            $loyalty->debit($user, abs($data['points']), 'admin_adjustment', $data['reason'], $reference);
        }

        return back()->with('success', 'Loyalty balance adjusted.');
    }

    public function exportCustomers()
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'role', 'orders', 'loyalty_points', 'referral_code', 'joined']);
            User::where('role', 'customer')->where('is_admin', false)->withCount('orders')->orderBy('id')->chunk(500, fn ($users) => $users->each(fn ($user) => fputcsv($file, [$user->name, $user->email, $user->role, $user->orders_count, $user->loyalty_points, $user->referral_code, $user->created_at])));
            fclose($file);
        }, 'customers.csv', ['Content-Type' => 'text/csv']);
    }

    public function reports(Request $request)
    {
        [$from, $to] = $this->reportRange($request);
        $base = Order::whereBetween('created_at', [$from, $to]);
        $paid = (clone $base)->where('payment_status', 'paid');
        $metrics = ['revenue' => (clone $paid)->sum('total_amount'), 'orders' => (clone $base)->count(), 'paid' => (clone $paid)->count(), 'discounts' => (clone $paid)->sum('discount_amount'), 'shipping' => (clone $paid)->sum('shipping_amount'), 'average' => (clone $paid)->avg('total_amount') ?: 0];
        $daily = (clone $paid)->selectRaw('DATE(created_at) day, SUM(total_amount) revenue, COUNT(*) orders')->groupBy('day')->orderBy('day')->get();
        $topProducts = OrderItem::selectRaw('product_name, SUM(quantity) units, SUM(total) revenue')->whereHas('order', fn ($query) => $query->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid'))->groupBy('product_name')->orderByDesc('revenue')->limit(10)->get();
        $couponStats = Coupon::withCount(['redemptions as redeemed_count' => fn ($query) => $query->where('status', 'redeemed')->whereBetween('redeemed_at', [$from, $to])])->orderByDesc('redeemed_count')->limit(10)->get();

        return view('admin.operations.reports', compact('metrics', 'daily', 'topProducts', 'couponStats', 'from', 'to'));
    }

    public function exportReports(Request $request)
    {
        [$from, $to] = $this->reportRange($request);

        return response()->streamDownload(function () use ($from, $to) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['order_number', 'date', 'customer', 'payment_method', 'payment_status', 'order_status', 'subtotal', 'discount', 'shipping', 'total']);
            Order::with('address')->whereBetween('created_at', [$from, $to])->orderBy('id')->chunk(500, fn ($orders) => $orders->each(fn ($order) => fputcsv($file, [$order->order_number, $order->created_at, $order->address?->email, $order->payment_method, $order->payment_status, $order->order_status, $order->subtotal, $order->discount_amount, $order->shipping_amount, $order->total_amount])));
            fclose($file);
        }, 'sales-report.csv', ['Content-Type' => 'text/csv']);
    }

    public function invoice(Order $order)
    {
        return view('admin.operations.invoice', ['order' => $order->load(['items', 'address'])]);
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function reportRange(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return [
            isset($data['from']) ? Carbon::parse($data['from'])->startOfDay() : now()->subDays(29)->startOfDay(),
            isset($data['to']) ? Carbon::parse($data['to'])->endOfDay() : now()->endOfDay(),
        ];
    }
}
