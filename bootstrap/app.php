<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            $rootDomain = env('ROOT_DOMAIN', 'spmbsmpdisdikporacianjur.local');

            // Main Domain
            Route::middleware('web')
                ->domain($rootDomain)
                ->group(base_path('routes/web.php'));

            // Auth Domain
            Route::middleware('web')
                ->domain('auth.' . $rootDomain)
                ->group(base_path('routes/auth.php'));

            // Admin Domain
            Route::middleware(['web', 'auth', 'two-factor', 'role:admin'])
                ->domain('admin.' . $rootDomain)
                ->group(base_path('routes/admin.php'));

            // OPSMP Domain (Dash)
            Route::middleware(['web', 'auth', 'two-factor', 'role:opsmp'])
                ->domain('dash.' . $rootDomain)
                ->group(base_path('routes/opsmp.php'));

            // OPSD Domain (Operator)
            Route::middleware(['web', 'auth', 'two-factor', 'role:opsd'])
                ->domain('operator.' . $rootDomain)
                ->group(base_path('routes/opsd.php'));

            // Siswa Domain (Pendaftaran)
            Route::middleware(['web'])
                ->domain('pendaftaran.' . $rootDomain)
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
