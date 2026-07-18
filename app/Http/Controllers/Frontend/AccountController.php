<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $transactions = $user->loyaltyTransactions()->latest()->paginate(10);
        $referralStats = ['signups' => $user->referrals()->count(), 'qualified' => $user->referrals()->whereHas('orders', fn ($query) => $query->where(fn ($q) => $q->whereNotNull('paid_at')->orWhereNotNull('delivered_at')))->count()];

        return view('frontend.account.profile', compact('user', 'transactions', 'referralStats'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()],
        ]);
        $emailChanged = $data['email'] !== $user->email;
        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
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
}
