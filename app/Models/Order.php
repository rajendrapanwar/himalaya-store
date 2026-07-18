<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $payment_expires_at
 * @property Carbon|null $delivered_at
 */
class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'coupon_id',
        'subtotal',
        'coupon_code',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'currency',
        'loyalty_points_redeemed',
        'loyalty_discount_amount',
        'payment_method',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'payment_status',
        'payment_expires_at',
        'paid_at',
        'inventory_reserved_at',
        'inventory_committed_at',
        'coupon_redeemed_at',
        'referral_rewarded_at',
        'order_status',
        'courier_name',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'customer_note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'loyalty_discount_amount' => 'decimal:2',
            'payment_expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'inventory_reserved_at' => 'datetime',
            'inventory_committed_at' => 'datetime',
            'coupon_redeemed_at' => 'datetime',
            'referral_rewarded_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return BelongsTo<Address, $this> */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function inventoryReservations(): HasMany
    {
        return $this->hasMany(InventoryReservation::class);
    }

    public function couponRedemption(): HasOne
    {
        return $this->hasOne(CouponRedemption::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /** @return HasOne<ReturnRequest, $this> */
    public function returnRequest(): HasOne
    {
        return $this->hasOne(ReturnRequest::class);
    }

    public function isReturnEligible(): bool
    {
        return $this->order_status === 'delivered' && $this->delivered_at?->gte(now()->subDays(30)) && ! $this->returnRequest;
    }
}
