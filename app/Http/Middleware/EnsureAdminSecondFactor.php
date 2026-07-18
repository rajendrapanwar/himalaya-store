<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSecondFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $verifiedAt = (int) $request->session()->get('admin_2fa_verified_at', 0);
        if ($verifiedAt < now()->subHours(8)->timestamp) {
            return redirect()->route('admin.two-factor.show');
        }

        return $next($request);
    }
}
