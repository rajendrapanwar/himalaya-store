<?php

use App\Http\Middleware\AuditAdminAction;
use App\Http\Middleware\EnsureAdminPermission;
use App\Http\Middleware\EnsureAdminSecondFactor;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'admin.audit' => AuditAdminAction::class,
            'admin.2fa' => EnsureAdminSecondFactor::class,
            'admin.permission' => EnsureAdminPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
