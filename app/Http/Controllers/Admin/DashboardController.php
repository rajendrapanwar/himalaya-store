<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'orders' => Order::count(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'today_orders' => Order::where('created_at', '>=', $today)->count(),
            'pending_orders' => Order::whereIn('order_status', ['pending', 'processing'])->count(),
        ];
        $recentOrders = Order::with('address')->latest()->take(6)->get();
        $lowStockProducts = Product::with('category')->where('stock_quantity', '<=', 10)->orderBy('stock_quantity')->take(6)->get();
        $statusCounts = Order::selectRaw('order_status, count(*) as total')->groupBy('order_status')->pluck('total', 'order_status');
        $salesTrend = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = now()->subDays($daysAgo);
            $orders = Order::whereDate('created_at', $date)->where('payment_status', 'paid');

            return [
                'label' => $date->format('D'),
                'orders' => (clone $orders)->count(),
                'revenue' => (float) (clone $orders)->sum('total_amount'),
            ];
        });
        $actionCounts = [
            'pending_reviews' => Review::where('moderation_status', 'pending')->count(),
            'open_returns' => ReturnRequest::whereIn('status', ['requested', 'approved'])->count(),
            'new_messages' => ContactMessage::where('status', 'new')->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts', 'statusCounts', 'salesTrend', 'actionCounts'));
    }
}
