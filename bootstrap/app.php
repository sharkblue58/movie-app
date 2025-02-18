<?php

use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle Unauthenticated Exception
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'You are Unauthenticated ! ',
            ], 401);
        });

        // Handle Unauthorized Exception
        $exceptions->renderable(function (AccessDeniedHttpException $e, $request) {
            return response()->json([
                'message' => 'You are Unauthorized !',
            ], 403);
        });

        // Handle Not Found Exception
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'message' => 'The requested Resource not found !',
            ], 404);
        });

        // Handle Validation Exception
        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        });


        $exceptions->renderable(function (UnsupportedMediaTypeHttpException $e, $request) {
            return response()->json([
                'message' => 'Unsupported media type. Please check the Content-Type header.',
            ], 415);
        });



        // Handle other exceptions
        $exceptions->renderable(function (Exception $e, $request) {
            return response()->json([
                'message' => 'Something went wrong, contact support.',
                'error' => $e->getMessage(),
            ], 500);
        });
    })->create();
