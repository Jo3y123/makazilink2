<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'         => \App\Http\Middleware\RoleMiddleware::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);
        $middleware->web(prepend: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\CheckSubscription::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'mpesa/callback',
            'mpesa/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            if (app()->environment('production') && !$request->expectsJson()) {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->view('errors.404', [], 404);
                }
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                    return response()->view('errors.403', [], 403);
                }
            }
        });
    })->create();