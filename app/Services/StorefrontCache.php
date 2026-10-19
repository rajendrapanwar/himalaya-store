<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class StorefrontCache
{
    private const VERSION_KEY = 'storefront:catalogue-version';

    public const NAVIGATION_KEY = 'navigation_categories:v2';

    public static function version(): int
    {
        return (int) Cache::rememberForever(self::VERSION_KEY, fn () => 1);
    }

    public static function bump(): void
    {
        Cache::forever(self::VERSION_KEY, self::version() + 1);
        Cache::forget(self::NAVIGATION_KEY);
    }
}
