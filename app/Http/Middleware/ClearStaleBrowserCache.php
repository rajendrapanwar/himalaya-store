<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tells each browser, once, to drop its HTTP cache for this site.
 *
 * Moving the store off /public/ left browsers holding permanent redirects
 * ("/ -> /public/" and "/public/ -> /") that loop without ever reaching the
 * server. Any page that does load clears them. Bump VERSION to repeat this
 * after another change to permanent redirects.
 */
class ClearStaleBrowserCache
{
    public const COOKIE = 'pr_cache_reset';

    public const VERSION = '2026-10-22';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET')
            || $request->expectsJson()
            || ! $response->isSuccessful()
            || $request->cookie(self::COOKIE) === self::VERSION) {
            return $response;
        }

        $response->headers->set('Clear-Site-Data', '"cache"');
        $response->headers->setCookie(Cookie::create(
            self::COOKIE,
            self::VERSION,
            now()->addYear(),
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            Cookie::SAMESITE_LAX,
        ));

        return $response;
    }
}
