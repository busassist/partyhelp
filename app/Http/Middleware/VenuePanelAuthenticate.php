<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

/**
 * Ensures unauthenticated venue panel requests always get a redirect to login,
 * even when the request expects JSON (e.g. Livewire). Otherwise after logout
 * the user could still see the panel layout with empty data instead of the login page.
 */
class VenuePanelAuthenticate extends \Filament\Http\Middleware\Authenticate
{
    /**
     * Always pass a redirect URL so the exception handler can redirect to login
     * instead of returning 401 JSON for Livewire/XHR requests.
     */
    protected function unauthenticated(Request $request, array $guards): void
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            Filament::getLoginUrl(),
        );
    }
}
