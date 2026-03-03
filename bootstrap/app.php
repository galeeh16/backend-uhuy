<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: [
            __DIR__.'/../routes/api.php',
            __DIR__.'/../routes/talent.php',
            __DIR__.'/../routes/company.php',
        ],
        web: null,
        commands: __DIR__.'/../routes/console.php',
        // apiPrefix: '/api',
        health: '/health',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // global middleware
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // api middleware group
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'token.expired' => \App\Http\Middleware\EnsureTokenNotExpired::class,
            'role' => \App\Http\Middleware\EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            \App\Exceptions\AlreadyAppliedException::class,
            \App\Exceptions\ForbiddenException::class,
            \App\Exceptions\NotFoundException::class,
        ]);

        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $e)  {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        });

        $exceptions->render(function (MissingAbilityException $e) {
            return response()->json([
                'message' => 'Insufficient permission.',
            ], 403);
        });

        $exceptions->render(function (AccessDeniedHttpException $e) {
            return response()->json([
                'message' => 'You are not allowed to perform this action.',
            ], 403);
        });

        $exceptions->render(function(NotFoundHttpException $e, Request $request) {
            return response()->json([
                'message' => 'Page not found for ' . $request->path() 
            ], 404);
        }); 

        $exceptions->render(function(MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'message' => 'Method not allowed for ' . $request->method() 
            ], 405);
        }); 
    })
    ->create();
