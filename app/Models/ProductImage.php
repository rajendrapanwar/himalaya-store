<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'is_main',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::bump());
        static::deleted(fn () => StorefrontCache::bump());
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
