<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            $redirectTo = $e->redirectTo($request);
            \Illuminate\Support\Facades\Log::channel('single')->debug('AuthenticationException render', [
                'path' => $request->path(),
                'redirect_to' => $redirectTo,
                'expects_json' => $request->expectsJson(),
                'xhr' => $request->header('X-Requested-With'),
            ]);
            if ($redirectTo === null) {
                return null;
            }
            // For XHR/Livewire, return 401 with redirect URL so the client can do a full-page redirect.
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Unauthenticated.', 'redirect' => $redirectTo], 401)
                    ->header('X-Redirect-To', $redirectTo);
            }
            return redirect()->guest($redirectTo);
        });
    })->create();
