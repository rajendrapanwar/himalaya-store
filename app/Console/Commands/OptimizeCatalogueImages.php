<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeCatalogueImages extends Command
{
    protected $signature = 'images:optimize-catalogue {--force : Rebuild existing WebP files}';

    protected $description = 'Create bounded, production-ready WebP versions of catalogue source images';

    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('PHP GD with WebP support is required.');

            return self::FAILURE;
        }

        $converted = 0;
        $directory = public_path('assets/img/products');
        foreach (File::files($directory) as $file) {
            if (! in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png'], true)) {
                continue;
            }

            $target = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilenameWithoutExtension().'.webp';
            if (File::exists($target) && ! $this->option('force')) {
                continue;
            }

            $info = @getimagesize($file->getPathname());
            $source = match ($info[2] ?? null) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($file->getPathname()),
                IMAGETYPE_PNG => @imagecreatefrompng($file->getPathname()),
                default => false,
            };
            if (! $source || ! $info) {
                $this->warn("Skipped unreadable image: {$file->getFilename()}");

                continue;
            }

            [$width, $height] = $info;
            $scale = min(1, 1400 / max($width, $height));
            $targetWidth = max(1, (int) round($width * $scale));
            $targetHeight = max(1, (int) round($height * $scale));
            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            imagewebp($canvas, $target, 82);
            imagedestroy($source);
            imagedestroy($canvas);
            $converted++;
        }

        $this->info("Created {$converted} optimized WebP catalogue images.");

        return self::SUCCESS;
    }
}
