<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;

/**
 * Ensures unauthenticated venue panel requests always get a redirect to login,
 * even when the request expects JSON (e.g. Livewire). Otherwise after logout
 * the user could still see the panel layout with empty data instead of the login page.
 */
class VenuePanelAuthenticate extends \Filament\Http\Middleware\Authenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();
        $guardName = Filament::getAuthGuard();
        $checked = $guard->check();
        $userId = $checked ? $guard->id() : null;

        Log::channel('single')->debug('VenuePanelAuthenticate', [
            'path' => $request->path(),
            'guard' => $guardName,
            'check' => $checked,
            'user_id' => $userId,
            'expects_json' => $request->expectsJson(),
        ]);

        if (! $checked) {
            $this->unauthenticated($request, $guards);
            return;
        }

        $user = $guard->user();
        if ($user && $user->role === 'admin') {
            Log::channel('single')->debug('VenuePanelAuthenticate: admin user on venue panel, redirecting to admin');
            $adminUrl = Filament::getPanel('admin')->getUrl();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    response()->json(['redirect' => $adminUrl], 403)->header('X-Redirect-To', $adminUrl)
                );
            }
            throw new \Illuminate\Http\Exceptions\HttpResponseException(redirect($adminUrl));
        }

        parent::authenticate($request, $guards);
    }

    /**
     * Always pass a redirect URL so the exception handler can redirect to login.
     * Use venue panel explicitly so we have a URL even if current panel isn't set yet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<string>  $guards
     */
    protected function unauthenticated($request, array $guards)
    {
        $loginUrl = Filament::getLoginUrl();
        if ($loginUrl === null || $loginUrl === '') {
            $venuePanel = Filament::getPanel('venue');
            $loginUrl = $venuePanel?->getLoginUrl() ?? url('/venue/login');
        }

        Log::channel('single')->debug('VenuePanelAuthenticate: unauthenticated, redirecting', [
            'path' => $request->path(),
            'redirect_to' => $loginUrl,
        ]);

        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $loginUrl,
        );
    }
}
