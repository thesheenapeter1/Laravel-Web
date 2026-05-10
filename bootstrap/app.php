<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            /*
            |--------------------------------------------------------------------------
            | Custom Middlewares
            |--------------------------------------------------------------------------
            | These aliases allow us to apply security and access rules in routes easily.
            */
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class, // Custom Admin-only check
            'cart.not_empty' => \App\Http\Middleware\RedirectIfCartEmpty::class, // New: Cart validation
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class, // New: Pro-security headers
            'no_back_history' => \App\Http\Middleware\PreventBackHistory::class, // New: Prevent back button after logout
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
