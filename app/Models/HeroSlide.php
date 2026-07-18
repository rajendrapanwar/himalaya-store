<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 */
class HeroSlide extends Model
{
    protected $fillable = [
        'eyebrow', 'title', 'description', 'image_path', 'image_alt',
        'primary_label', 'primary_url', 'secondary_label', 'secondary_url',
        'text_theme', 'text_position', 'sort_order', 'is_active', 'starts_at', 'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::bump());
        static::deleted(fn () => StorefrontCache::bump());
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function getImageUrlAttribute(): string
    {
        return str_starts_with($this->image_path, 'assets/')
            ? asset($this->image_path)
            : asset('storage/'.$this->image_path);
    }

    public function getStatusAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }
        if ($this->starts_at?->isFuture()) {
            return 'Scheduled';
        }
        if ($this->ends_at?->isPast()) {
            return 'Expired';
        }

        return 'Live';
    }
}
