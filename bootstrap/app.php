<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'nocache' => \App\Http\Middleware\NoCache::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'teams' => \App\Http\Middleware\TeamsPermission::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
