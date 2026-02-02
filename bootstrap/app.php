<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Рендерим все исключения для API
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'message' => 'Unauthenticated.'
                    ], 401);
                }

                if ($e instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors'  => $e->errors(),
                    ], 422);
                }

                // 2. Обработка 404 (модель не найдена)
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found',
                    ], 404);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Server Error',
                    'debug'   => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.'
                ], 500);
            }
        });
    })->create();
