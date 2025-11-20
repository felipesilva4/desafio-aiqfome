<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $errors = $e->errors();

                if (isset($errors['email'])) {
                    $emailError = $errors['email'][0] ?? '';
                    if ($emailError == 'The email has already been taken.') {
                        return response()->json([
                            'message' => 'Este email já está cadastrado.',
                        ], 409);
                    }
                }

                return response()->json([
                    'message' => 'Os dados fornecidos são inválidos.',
                    'errors' => $errors,
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Não autenticado.',
                    'error' => 'unauthenticated',
                ], 401);
            }
        });
    })->create();
