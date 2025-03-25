<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global para todas las solicitudes
        $middleware->use([
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // Grupo de middleware para rutas de API (sin auth:sanctum)
        $middleware->api([
            'throttle',
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Definir alias para middleware
        $middleware->alias([
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejar excepciones y forzar respuestas JSON para rutas de API
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = 500;

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $statusCode = 401;
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                    return response()->json([
                        'data' => [],
                        'status' => false,
                        'message' => 'Error de validación',
                        'errors' => $e->errors(),
                    ], $statusCode);
                } elseif ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                    $statusCode = 422;
                    return response()->json([
                        'data' => [],
                        'status' => false,
                        'message' => 'Error de base de datos: Integrity constraint violation (posiblemente category_id no existe).',
                    ], $statusCode);
                }

                return response()->json([
                    'data' => [],
                    'status' => false,
                    'message' => $e->getMessage() ?: 'Server Error',
                ], $statusCode);
            }
        });
    })->create();