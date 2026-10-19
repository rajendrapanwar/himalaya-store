<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'seo_title',
        'meta_description',
        'focus_keyword',
        'image',
        'status',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::bump());
        static::deleted(fn () => StorefrontCache::bump());
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
