<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;

/**
 * Admin panel auth: only admin role can access. Venue users are redirected to the venue panel.
 */
class AdminPanelAuthenticate extends \Filament\Http\Middleware\Authenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();
        $checked = $guard->check();

        if (! $checked) {
            $this->unauthenticated($request, $guards);
            return;
        }

        $user = $guard->user();
        if ($user && $user->role === 'venue') {
            Log::channel('single')->debug('AdminPanelAuthenticate: venue user on admin panel, redirecting to venue');
            $venueUrl = Filament::getPanel('venue')->getUrl();
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    response()->json(['redirect' => $venueUrl], 403)->header('X-Redirect-To', $venueUrl)
                );
            }
            throw new \Illuminate\Http\Exceptions\HttpResponseException(redirect($venueUrl));
        }

        parent::authenticate($request, $guards);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  array<string>  $guards
     */
    protected function unauthenticated($request, array $guards)
    {
        $loginUrl = Filament::getLoginUrl();
        if ($loginUrl === null || $loginUrl === '') {
            $adminPanel = Filament::getPanel('admin');
            $loginUrl = $adminPanel?->getLoginUrl() ?? url('/admin/login');
        }

        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $loginUrl,
        );
    }
}
