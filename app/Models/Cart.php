<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $reminded_at
 */
class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return ['reminded_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<CartItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /** @return HasMany<CartBundleItem, $this> */
    public function bundleItems(): HasMany
    {
        return $this->hasMany(CartBundleItem::class);
    }

    public function belongsToCurrentVisitor(): bool
    {
        return $this->session_id === session()->getId()
            || (auth()->check() && $this->user_id === auth()->id());
    }
}
