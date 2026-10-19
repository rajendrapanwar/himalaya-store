<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JournalPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'author_name', 'excerpt', 'content',
        'featured_image', 'image_alt', 'seo_title', 'meta_description', 'focus_keyword',
        'is_featured', 'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::bump());
        static::deleted(fn () => StorefrontCache::bump());
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 200));
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        return str_starts_with($this->featured_image, 'assets/')
            ? asset($this->featured_image)
            : asset('storage/'.$this->featured_image);
    }
}
