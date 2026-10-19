<?php

namespace App\Http\Middleware;

use App\Services\MaintenanceModeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StorefrontMaintenanceMode
{
    public function __construct(private readonly MaintenanceModeService $maintenance) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request) || ! $this->maintenance->enabled()) {
            return $next($request);
        }

        $status = $this->maintenance->status();
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $status['message'],
                'maintenance' => true,
                'expected_return_at' => $status['expected_return_at'],
            ], 503, ['Retry-After' => '3600']);
        }

        return response()
            ->view('errors.maintenance', ['maintenance' => $status], 503)
            ->header('Retry-After', '3600')
            ->header('Cache-Control', 'no-store, private, max-age=0')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function shouldBypass(Request $request): bool
    {
        if ($request->user()?->isStaff()) {
            return true;
        }

        return $request->routeIs(
            'admin.*',
            'password.*',
            'logout',
            'stripe.webhook',
        );
    }
}
