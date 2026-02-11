<?php

namespace App\Auth;

use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as Contract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class VenueRegistrationResponse implements Contract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->route('venue.registration-received');
    }
}
