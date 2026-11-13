<?php

namespace App\Services;

use GdImage;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Smaller WebP copies of catalogue images, so a page can offer the browser a
 * size that fits (srcset) instead of always sending the full upload.
 *
 * A source is either an upload on the public disk ("products/x.jpg") or a
 * static file under public/ ("assets/img/hero/x.webp"). Every variant lives on
 * the public disk as variants/{width}/{source path without extension}.webp, so
 * nothing generated is committed and deploys never overwrite it.
 */
class ImageVariants
{
    /** 480 covers cards on phones; 960 covers cards, tiles and the product photo on desktop. */
    public const WIDTHS = [480, 960];

    public const DIRECTORY = 'variants';

    private const QUALITY = 78;

    /** Decoding needs about five bytes per pixel; skip anything that could exhaust memory. */
    private const MAX_PIXELS = 40_000_000;

    /** @var array<string, true>|null Variant files that exist, read once per request. */
    private ?array $existing = null;

    public function variantPath(string $image, int $width): string
    {
        return self::DIRECTORY.'/'.$width.'/'.preg_replace('/\.[^.\/]+$/', '', ltrim($image, '/')).'.webp';
    }

    public function url(string $image): string
    {
        return $this->isAsset($image) ? asset($image) : asset('storage/'.$image);
    }

    /**
     * The srcset for an image, or null when it has no variants (not generated
     * yet, too small to need them, or not a raster image).
     *
     * Pass $withOriginal for the few large slots (product photo, page banner)
     * where the full upload should stay available to sharp screens. It reads
     * the file's dimensions, so keep it off long lists.
     */
    public function srcset(?string $image, bool $withOriginal = false): ?string
    {
        if (! $image) {
            return null;
        }

        $candidates = [];
        foreach (self::WIDTHS as $width) {
            $variant = $this->variantPath($image, $width);
            if (isset($this->existing()[$variant])) {
                $candidates[] = asset('storage/'.$variant).' '.$width.'w';
            }
        }
        if (! $candidates) {
            return null;
        }

        if ($withOriginal && ($info = @getimagesize($this->sourceFile($image))) && $info[0] > max(self::WIDTHS)) {
            $candidates[] = $this->url($image).' '.$info[0].'w';
        }

        return implode(', ', $candidates);
    }

    /**
     * Create the missing variants for one image and return how many were
     * written. Never upscales, and never throws: a photo that cannot be
     * processed simply keeps being served at full size.
     */
    public function generate(string $image, bool $force = false): int
    {
        if (! function_exists('imagewebp')) {
            return 0;
        }

        $source = $this->sourceFile($image);
        if (! is_file($source)) {
            return 0;
        }

        try {
            $info = @getimagesize($source);
            if (! $info || $info[0] * $info[1] > self::MAX_PIXELS) {
                return 0;
            }
            [$sourceWidth, $sourceHeight] = $info;

            $pending = [];
            foreach (self::WIDTHS as $width) {
                $variant = $this->variantPath($image, $width);
                if ($width < $sourceWidth && ($force || ! Storage::disk('public')->exists($variant))) {
                    $pending[$width] = $variant;
                }
            }
            if (! $pending) {
                return 0;
            }

            $original = $this->decode($source, $info[2]);
            if (! $original) {
                return 0;
            }

            $written = 0;
            foreach ($pending as $width => $variant) {
                $height = max(1, (int) round($sourceHeight * $width / $sourceWidth));
                $canvas = imagecreatetruecolor($width, $height);
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                imagecopyresampled($canvas, $original, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

                Storage::disk('public')->makeDirectory(dirname($variant));
                if (imagewebp($canvas, Storage::disk('public')->path($variant), self::QUALITY)) {
                    $written++;
                }
                imagedestroy($canvas);
            }
            imagedestroy($original);
            $this->existing = null;

            return $written;
        } catch (Throwable $exception) {
            report($exception);

            return 0;
        }
    }

    /**
     * Remove an upload's variants after it is replaced or deleted. Static
     * assets are left alone: several records can share one file.
     */
    public function forget(?string $image): void
    {
        if (! $image || $this->isAsset($image)) {
            return;
        }

        Storage::disk('public')->delete(array_map(fn (int $width): string => $this->variantPath($image, $width), self::WIDTHS));
        $this->existing = null;
    }

    private function isAsset(string $image): bool
    {
        return str_starts_with(ltrim($image, '/'), 'assets/');
    }

    private function sourceFile(string $image): string
    {
        return $this->isAsset($image)
            ? public_path(ltrim($image, '/'))
            : Storage::disk('public')->path($image);
    }

    private function decode(string $file, int $type): ?GdImage
    {
        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file),
            IMAGETYPE_PNG => @imagecreatefrompng($file),
            IMAGETYPE_WEBP => @imagecreatefromwebp($file),
            default => false,
        };
        if (! $image) {
            return null;
        }
        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        return $image;
    }

    /** @return array<string, true> */
    private function existing(): array
    {
        return $this->existing ??= array_fill_keys(Storage::disk('public')->allFiles(self::DIRECTORY), true);
    }
}
