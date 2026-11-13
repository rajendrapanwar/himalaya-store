<?php

namespace App\Console\Commands;

use App\Observers\ImageVariantObserver;
use App\Services\ImageVariants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateImageVariants extends Command
{
    protected $signature = 'images:variants {--force : Rebuild variants that already exist}';

    protected $description = 'Create smaller WebP copies of catalogue images for responsive srcset';

    /** Static folders whose images are used directly by storefront views. */
    private const ASSET_FOLDERS = ['assets/img/hero', 'assets/img/editorial'];

    /** Single static files the layout and home page reference by name. */
    private const ASSET_FILES = [
        'assets/img/products/wild-forest-honey.webp',
        'assets/img/products/munsiyari-rajma.webp',
        'assets/img/products/ringal-basket.webp',
        'assets/img/products/multiflora-honey.webp',
    ];

    public function handle(ImageVariants $variants): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('PHP GD with WebP support is required.');

            return self::FAILURE;
        }

        $images = collect();
        foreach (ImageVariantObserver::ATTRIBUTES as $model => $attribute) {
            $images = $images->merge($model::query()->whereNotNull($attribute)->pluck($attribute));
        }
        foreach (self::ASSET_FOLDERS as $folder) {
            foreach (File::isDirectory(public_path($folder)) ? File::files(public_path($folder)) : [] as $file) {
                $images->push($folder.'/'.$file->getFilename());
            }
        }

        $images = $images->merge(self::ASSET_FILES)->filter()->unique()->values();
        $written = 0;
        foreach ($images as $image) {
            $written += $variants->generate($image, (bool) $this->option('force'));
        }

        $this->info("Checked {$images->count()} images and wrote {$written} variants.");

        return self::SUCCESS;
    }
}
