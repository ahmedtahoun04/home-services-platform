<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin pages (HTML): URL prefix /admin, route names admin.*,
            // only for logged-in administrators. The routes are in routes/admin.php.
            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API clients (and any request asking for JSON) always get JSON errors.
        $wantsJson = fn (Request $request): bool => $request->is('api/*') || $request->expectsJson();

        $exceptions->shouldRenderJsonWhen($wantsJson);

        // Unified error format: {"message": "..."} (+ "errors" for validation).

        // A record that does not exist (e.g. GET /api/services/9999).
        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($wantsJson) {
            if ($wantsJson($request)) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
        });

        // 403, 404, 405, 409, 429, ... thrown by abort() or the framework.
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($wantsJson) {
            if (! $wantsJson($request)) {
                return null;
            }

            $status = $e->getStatusCode();

            $message = match (true) {
                $status === 404 => 'Resource not found.',
                $status === 405 => 'Method not allowed.',
                $status === 429 => 'Too many requests.',
                $e->getMessage() !== '' => $e->getMessage(),
                default => Response::$statusTexts[$status] ?? 'Error',
            };

            return response()->json(['message' => $message], $status, $e->getHeaders());
        });
    })->create();
