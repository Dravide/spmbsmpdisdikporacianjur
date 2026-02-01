<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'two-factor', 'role:admin'])
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web', 'auth', 'two-factor', 'role:opsd'])
                ->group(base_path('routes/opsd.php'));

            Route::middleware(['web', 'auth', 'two-factor', 'role:opsmp'])
                ->group(base_path('routes/opsmp.php'));

            Route::middleware(['web', 'auth:siswa'])
                ->group(base_path('routes/siswa.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'session.activity' => \App\Http\Middleware\SessionActivityMiddleware::class,
            'two-factor' => \App\Http\Middleware\EnsureTwoFactorEnabled::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SessionActivityMiddleware::class,
            \App\Http\Middleware\CheckSiteMode::class,
        ]);

        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
