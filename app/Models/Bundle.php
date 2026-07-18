<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bundle extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'price', 'image', 'is_active'];

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::bump());
        static::deleted(fn () => StorefrontCache::bump());
    }

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    /** @return BelongsToMany<Product, $this, BundleProduct, 'pivot'> */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->using(BundleProduct::class)->withPivot('quantity');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartBundleItem::class);
    }

    public function getAvailableStockAttribute(): int
    {
        $this->loadMissing('products');
        if ($this->products->isEmpty()) {
            return 0;
        }

        return (int) $this->products->min(fn (Product $product) => intdiv($product->available_stock, max(1, (int) $product->pivot->quantity)));
    }

    public function getRetailValueAttribute(): float
    {
        $this->loadMissing('products');

        return round((float) $this->products->sum(fn (Product $product) => (float) ($product->sale_price ?? $product->price) * max(1, (int) $product->pivot->quantity)), 2);
    }

    public function getSavingsAttribute(): float
    {
        return max(0, round($this->retail_value - (float) $this->price, 2));
    }
}
