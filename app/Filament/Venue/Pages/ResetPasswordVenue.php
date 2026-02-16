<?php

namespace App\Filament\Venue\Pages;

use Filament\Auth\Http\Responses\Contracts\PasswordResetResponse;
use Filament\Auth\Pages\PasswordReset\ResetPassword as BaseResetPassword;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class ResetPasswordVenue extends BaseResetPassword
{
    /**
     * Ensure email is set from the URL so credentials on submit are correct.
     */
    public function mount(?string $email = null, ?string $token = null): void
    {
        parent::mount($email, $token);

        $resolved = $email ?? request()->query('email');
        if ($resolved !== null) {
            $this->email = $resolved;
        } elseif (isset($this->form->getState()['email'])) {
            $this->email = $this->form->getState()['email'];
        }
    }

    public function resetPassword(): ?PasswordResetResponse
    {
        try {
            $this->rateLimit(2);
        } catch (\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $data['email'] = $this->email ?? $data['email'] ?? null;
        $data['token'] = $this->token;

        $hasPanelAccess = true;
        $status = Password::broker(Filament::getAuthPasswordBroker())->reset(
            $this->getCredentialsFromFormData($data),
            function (CanResetPassword|Model|Authenticatable $user) use ($data, &$hasPanelAccess): void {
                if (
                    ($user instanceof FilamentUser) &&
                    (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel()))
                ) {
                    $hasPanelAccess = false;

                    return;
                }
                $user->forceFill([
                    $user->getAuthPasswordName() => Hash::make($data['password']),
                    $user->getRememberTokenName() => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        if ($hasPanelAccess === false) {
            $status = Password::INVALID_USER;
        }

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title(__(Password::PASSWORD_RESET))
                ->success()
                ->send();

            return app(PasswordResetResponse::class);
        }

        if ($status === Password::INVALID_TOKEN) {
            $requestUrl = Filament::getPanel('venue')->getRequestPasswordResetUrl();
            Notification::make()
                ->title(__('This reset link is invalid or has expired.'))
                ->body(new HtmlString(
                    'Please <a href="' . e($requestUrl) . '" class="underline font-medium">request a new password reset link</a> and try again. Links expire after 60 minutes.'
                ))
                ->danger()
                ->send();
        } else {
            Notification::make()
                ->title(__($status))
                ->danger()
                ->send();
        }

        return null;
    }
}
