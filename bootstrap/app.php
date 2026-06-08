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
    ->withMiddleware(function ($middleware) {
    $middleware->alias([
        'role.check' => \App\Http\Middleware\CheckUserRole::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

    $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    });

    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    });

    $exceptions->render(function (\Throwable $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error' => app()->environment('local') ? $e->getMessage() : null,
        ], 500);
    });

    $exceptions->render(function(\Illuminate\Auth\AuthenticationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
        ], 401);
    });
    })->create();
