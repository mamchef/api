<?php

use App\Http\Middleware\AuthChefSanctum;
use App\Http\Middleware\AuthUserSanctum;
use App\Http\Middleware\NormalizeResponseMiddleware;
use App\Http\Middleware\SetUserSanctum;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(NormalizeResponseMiddleware::class);
        $middleware->alias([
            "chef-auth" => AuthChefSanctum::class,
            "user-auth" => AuthUserSanctum::class,
            "set-user-auth" => SetUserSanctum::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
