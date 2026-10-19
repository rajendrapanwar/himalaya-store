<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // Preserve compatibility with the original is_admin flag while role-based
        // staff accounts use the more granular role column.
        $role = $user->is_admin && in_array($user->role, [null, 'customer', 'administrator'], true) ? 'administrator' : $user->role;
        $route = (string) $request->route()?->getName();
        if (str_starts_with($route, 'admin.maintenance.')) {
            abort_unless($role === 'administrator', 403, 'Only a full administrator can change storefront availability.');

            return $next($request);
        }
        if ($role === 'administrator') {
            return $next($request);
        }
        if (str_starts_with($route, 'admin.notifications.')) {
            return $next($request);
        }
        $write = ! in_array($request->method(), ['GET', 'HEAD'], true);
        if (in_array($route, ['admin.orders.refund', 'admin.customers.points'], true)) {
            abort_unless(in_array($role, ['administrator', 'manager'], true), 403, 'Only administrators and managers can perform this financial action.');

            return $next($request);
        }
        $allowed = match ($role) {
            'manager' => ! str_starts_with($route, 'admin.team.'),
            'catalogue' => str_starts_with($route, 'admin.products.') || str_starts_with($route, 'admin.categories.') || str_starts_with($route, 'admin.bundles.') || str_starts_with($route, 'admin.journals.') || str_starts_with($route, 'admin.hero-slides.') || $route === 'admin.dashboard',
            'fulfillment' => str_starts_with($route, 'admin.orders.') || str_starts_with($route, 'admin.returns.') || str_starts_with($route, 'admin.inventory.') || str_starts_with($route, 'admin.serviceability.') || $route === 'admin.dashboard',
            'support' => str_starts_with($route, 'admin.customers.') || str_starts_with($route, 'admin.reviews.') || str_starts_with($route, 'admin.contact-messages.') || str_starts_with($route, 'admin.returns.') || (! $write && str_starts_with($route, 'admin.orders.')) || $route === 'admin.dashboard',
            default => false,
        };
        abort_unless($allowed, 403, 'Your staff role does not have permission for this action.');

        return $next($request);
    }
}
