<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('admin*') || $request->expectsJson()) {
                return null;
            }

            return response()->view('errors.404', status: 404);
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('admin*') || $request->expectsJson()) {
                return null;
            }

            return match ($e->getStatusCode()) {
                403 => response()->view('errors.403', status: 403),
                419 => response()->view('errors.419', status: 419),
                429 => response()->view('errors.429', status: 429),
                default => null,
            };
        });
    })->create();
