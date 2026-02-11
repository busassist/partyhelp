<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class VenueSetPasswordController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (! $token || ! $email) {
            return redirect()->route('filament.venue.auth.login')
                ->with('error', 'Invalid or expired set-password link.');
        }

        return view('venue.set-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = User::where('email', $validated['email'])->where('role', 'venue')->first();

        if (! $user || ! Password::broker()->tokenExists($user, $validated['token'])) {
            return back()->withErrors(['email' => 'This set-password link is invalid or has expired.']);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        Password::broker()->deleteToken($user);

        return redirect()->route('filament.venue.auth.login')
            ->with('status', 'Password set. You can now sign in.');
    }
}
