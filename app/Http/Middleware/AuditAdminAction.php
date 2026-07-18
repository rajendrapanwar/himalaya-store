<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditAdminAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true) && $response->getStatusCode() < 400) {
            $parameters = collect($request->route()?->parameters() ?? [])->map(fn ($value) => $value instanceof Model ? ['type' => $value::class, 'id' => $value->getKey()] : (is_scalar($value) ? $value : get_debug_type($value)))->all();
            AdminActivityLog::create([
                'user_id' => $request->user()?->id,
                'action' => $request->route()?->getName() ?: $request->method().' '.$request->path(),
                'after' => ['route_parameters' => $parameters, 'input_keys' => array_keys($request->except(['password', 'password_confirmation', '_token']))],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}
