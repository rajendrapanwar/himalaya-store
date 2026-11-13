<?php

namespace App\Observers;

use App\Models\Bundle;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\JournalPost;
use App\Models\ProductImage;
use App\Services\ImageVariants;
use Illuminate\Database\Eloquent\Model;

/**
 * Keeps the smaller WebP copies in step with the photos staff upload: made
 * when an image is saved, removed when it is replaced or deleted.
 */
class ImageVariantObserver
{
    /** Which attribute holds the image path on each model. */
    public const ATTRIBUTES = [
        ProductImage::class => 'image',
        Category::class => 'image',
        Bundle::class => 'image',
        HeroSlide::class => 'image_path',
        JournalPost::class => 'featured_image',
    ];

    public function __construct(private readonly ImageVariants $variants) {}

    public function saved(Model $model): void
    {
        $attribute = self::ATTRIBUTES[$model::class] ?? null;
        if (! $attribute || ! ($model->wasRecentlyCreated || $model->wasChanged($attribute))) {
            return;
        }

        // `saved` fires before the original values are synced, so this is still the old path.
        $previous = $model->getOriginal($attribute);
        $current = $model->getAttribute($attribute);

        if (is_string($previous) && $previous !== $current) {
            $this->variants->forget($previous);
        }
        if (is_string($current) && $current !== '') {
            $this->variants->generate($current);
        }
    }

    public function deleted(Model $model): void
    {
        $attribute = self::ATTRIBUTES[$model::class] ?? null;
        $path = $attribute ? $model->getAttribute($attribute) : null;
        if (is_string($path)) {
            $this->variants->forget($path);
        }
    }
}
