<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
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

        $contentSecurityPolicy = "default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; frame-src https://www.openstreetmap.org https://checkout.stripe.com; connect-src 'self' https://api.stripe.com; object-src 'none'; base-uri 'self'; form-action 'self' https://checkout.stripe.com; frame-ancestors 'none'; manifest-src 'self'; worker-src 'self' blob:";
        if ($request->isSecure() && app()->environment('production')) {
            $contentSecurityPolicy .= '; upgrade-insecure-requests';
        }
        $response->headers->set('Content-Security-Policy', $contentSecurityPolicy);

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
