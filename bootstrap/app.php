<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Http\Middleware\HandleCors;
use PhpOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider;  

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        LaravelServiceProvider::class,  
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prependToGroup('api', HandleCors::class);
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth:api' => \PhpOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.auth' => \PhpOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \PhpOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken::class,
            'jwt.cookie' => App\Http\Middleware\JwtCookieMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
