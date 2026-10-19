<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** @property BundleProduct $pivot */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'seo_title',
        'meta_description',
        'focus_keyword',
        'price',
        'sale_price',
        'sku',
        'producer_name', 'producer_location', 'batch_number', 'harvested_at', 'ingredients',
        'stock_quantity',
        'reserved_quantity',
        'weight',
        'origin_region',
        'is_featured',
        'status',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::bump());
        static::deleted(fn () => StorefrontCache::bump());
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /** @return HasOne<ProductImage, $this> */
    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', 1);
    }

    public function displayImage(): ?ProductImage
    {
        return $this->mainImage ?: $this->images->first();
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class)->using(BundleProduct::class)->withPivot('quantity');
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'harvested_at' => 'date',
            'is_featured' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }
}
