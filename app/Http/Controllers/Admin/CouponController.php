<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', ['coupons' => Coupon::withCount('redemptions')->latest()->paginate(20)]);
    }

    public function create()
    {
        return $this->form(new Coupon);
    }

    public function edit(Coupon $coupon)
    {
        return $this->form($coupon->load(['products', 'categories']));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $coupon = Coupon::create($this->data($request));
            $this->syncRestrictions($coupon, $request);
        });

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        DB::transaction(function () use ($request, $coupon) {
            $coupon->update($this->data($request, $coupon));
            $this->syncRestrictions($coupon, $request);
        });

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->redemptions()->exists()) {
            return back()->with('error', 'Coupons with redemption history cannot be deleted. Disable the coupon instead.');
        }
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    private function form(Coupon $coupon)
    {
        return view('admin.coupons.form', ['coupon' => $coupon, 'categories' => Category::orderBy('name')->get(), 'products' => Product::orderBy('name')->get()]);
    }

    private function data(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:50', Rule::unique('coupons')->ignore($coupon)],
            'type' => 'required|in:fixed,percent',
            'value' => ['required', 'numeric', 'gt:0', Rule::when($request->input('type') === 'percent', ['max:100'])],
            'max_discount_amount' => 'nullable|numeric|min:0', 'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1', 'per_user_limit' => 'required|integer|min:1|max:100',
            'first_order_only' => 'required|boolean', 'free_shipping' => 'required|boolean',
            'starts_at' => 'nullable|date', 'expires_at' => 'nullable|date|after:starts_at', 'is_active' => 'required|boolean',
            'categories' => 'nullable|array', 'categories.*' => 'exists:categories,id', 'products' => 'nullable|array', 'products.*' => 'exists:products,id',
        ]);
        unset($data['categories'], $data['products']);
        $data['code'] = strtoupper($data['code']);

        return $data;
    }

    private function syncRestrictions(Coupon $coupon, Request $request): void
    {
        $coupon->categories()->sync($request->input('categories', []));
        $coupon->products()->sync($request->input('products', []));
    }
}
