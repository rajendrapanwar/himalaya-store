<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // A fresh nonce per response lets the script policy drop 'unsafe-inline':
        // Vite tags and the few deliberate inline scripts carry it, injected markup cannot.
        $nonce = Vite::useCspNonce();

        $response = $next($request);
        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self "https://checkout.stripe.com"), usb=(), browsing-topics=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Origin-Agent-Cluster', '?1');

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($request, $nonce));

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($this->isPrivateRoute($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            $response->headers->set('Cache-Control', 'no-store, private, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    private function contentSecurityPolicy(Request $request, string $nonce): string
    {
        // Inline style attributes are still used for layout details, so styles keep
        // 'unsafe-inline'; scripts are restricted to this origin and the nonce.
        $scriptSources = ["'self'", "'nonce-{$nonce}'"];
        $styleSources = ["'self'", "'unsafe-inline'"];
        $connectSources = ["'self'", 'https://api.stripe.com'];

        if (Vite::isRunningHot()) {
            $devServer = rtrim(trim((string) file_get_contents(Vite::hotFile())), '/');
            $scriptSources[] = $devServer;
            $styleSources[] = $devServer;
            $connectSources[] = $devServer;
            $connectSources[] = preg_replace('/^http/', 'ws', $devServer);
        }

        $directives = [
            "default-src 'self'",
            "img-src 'self' data: https:",
            'style-src '.implode(' ', $styleSources),
            'script-src '.implode(' ', $scriptSources),
            "font-src 'self' data:",
            'frame-src https://www.openstreetmap.org https://checkout.stripe.com',
            'connect-src '.implode(' ', $connectSources),
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https://checkout.stripe.com",
            "frame-ancestors 'none'",
            "manifest-src 'self'",
            "worker-src 'self' blob:",
        ];
        if ($request->isSecure() && app()->environment('production')) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }

    private function isPrivateRoute(Request $request): bool
    {
        return $request->routeIs(
            'admin.*',
            'cart.*',
            'checkout.*',
            'stripe.checkout',
            'login',
            'register',
            'logout',
            'password.*',
            'verification.*',
            'profile',
            'profile.*',
            'orders',
            'orders.*',
            'wishlist.*',
            'notifications.*',
            'returns.store'
        );
    }
}
