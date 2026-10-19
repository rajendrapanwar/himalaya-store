<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\AccountActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $transactions = $user->loyaltyTransactions()->latest()->paginate(10);
        $referralStats = ['signups' => $user->referrals()->count(), 'qualified' => $user->referrals()->whereHas('orders', fn ($query) => $query->where(fn ($q) => $q->whereNotNull('paid_at')->orWhereNotNull('delivered_at')))->count()];

        $latestOrder = $user->orders()
            ->with(['items', 'address', 'statusHistory'])
            ->latest()
            ->first();
        $dashboardStats = [
            'orders' => $user->orders()->count(),
            'active_orders' => $user->orders()->whereIn('order_status', ['pending', 'processing', 'shipped'])->count(),
            'wishlist' => $user->wishlist()->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
            'reward_value' => round($user->loyalty_points * (float) config('commerce.loyalty.rupees_per_point'), 2),
        ];
        $recommendedProducts = Product::with(['category', 'images'])
            ->withCount(['reviews as approved_reviews_count' => fn ($query) => $query->where('is_approved', true)])
            ->withAvg(['reviews as approved_reviews_avg' => fn ($query) => $query->where('is_approved', true)], 'rating')
            ->where('status', true)
            ->whereRaw('(stock_quantity - reserved_quantity) > 0')
            ->orderByDesc('is_featured')
            ->latest()
            ->take(4)
            ->get();
        $wishlistProductIds = $user->wishlist()->pluck('products.id')->all();

        return view('frontend.account.profile', compact(
            'user', 'transactions', 'referralStats', 'latestOrder', 'dashboardStats',
            'recommendedProducts', 'wishlistProductIds'
        ));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=128,min_height=128,max_width=3000,max_height=3000'],
            'remove_avatar' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()],
        ]);
        $emailChanged = $data['email'] !== $user->email;
        $passwordChanged = ! empty($data['password']);
        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $previousAvatar = $user->avatar_path;
        if ($request->hasFile('avatar')) {
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $user->avatar_path = null;
        }
        $user->save();
        if ($previousAvatar && $previousAvatar !== $user->avatar_path) {
            Storage::disk('public')->delete($previousAvatar);
        }
        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            $user->notify(new AccountActivityNotification(
                'Email address updated',
                'Your account email was changed. Verify the new address before checking out.',
                route('profile'),
                'security',
            ));
        }
        if ($passwordChanged) {
            $user->notify(new AccountActivityNotification(
                'Your password was changed',
                'The password for your Pahadi Roots account was changed successfully. Contact us immediately if this was not you.',
                route('profile'),
                'security',
                true,
            ));
        }

        return back()->with('success', 'Your profile has been updated.');
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with('address')->latest()->paginate(10);

        return view('frontend.account.orders', compact('orders'));
    }

    public function order(Request $request, Order $order)
    {
        Gate::authorize('view', $order);
        $order->load(['items', 'address', 'statusHistory', 'returnRequest']);

        return view('frontend.account.order', compact('order'));
    }

    public function track(Request $request, Order $order)
    {
        Gate::authorize('view', $order);
        $order->load(['items', 'address', 'statusHistory']);

        $historyTimes = [];
        foreach ($order->statusHistory->sortBy('created_at') as $history) {
            $historyTimes[$history->to_status] ??= $history->created_at;
        }
        $currentRank = match ($order->order_status) {
            'processing' => 1,
            'shipped' => 2,
            'delivered' => 3,
            default => 0,
        };
        $trackingSteps = [
            ['key' => 'pending', 'title' => 'Order placed', 'description' => 'We have received your order.', 'time' => $order->created_at],
            ['key' => 'processing', 'title' => 'Packed with care', 'description' => 'Your mountain produce is being checked and packed.', 'time' => $historyTimes['processing'] ?? $order->paid_at],
            ['key' => 'shipped', 'title' => 'On the way', 'description' => 'Your parcel has left our fulfilment centre.', 'time' => $order->shipped_at ?? $historyTimes['shipped'] ?? null],
            ['key' => 'delivered', 'title' => 'Delivered', 'description' => 'Your order has reached its destination.', 'time' => $order->delivered_at ?? $historyTimes['delivered'] ?? null],
        ];
        foreach ($trackingSteps as $index => &$step) {
            $step['state'] = $order->order_status === 'cancelled'
                ? ($index === 0 ? 'complete' : 'upcoming')
                : ($index < $currentRank ? 'complete' : ($index === $currentRank ? 'current' : 'upcoming'));
        }
        unset($step);

        return view('frontend.account.track', compact('order', 'trackingSteps'));
    }
}
