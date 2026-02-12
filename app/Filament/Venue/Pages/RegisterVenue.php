<?php

namespace App\Filament\Venue\Pages;

use App\Auth\VenueRegistrationResponse;
use App\Jobs\SendVenueApprovalEmail;
use App\Models\User;
use App\Models\Venue;
use App\Services\DebugLogService;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class RegisterVenue extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                TextInput::make('business_name')
                    ->label('Business / venue name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('contact_name')
                    ->label('Contact name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('contact_phone')
                    ->label('Contact phone')
                    ->tel()
                    ->required()
                    ->maxLength(50),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['role'] = 'venue';

        return $data;
    }

    protected function handleRegistration(array $data): \Illuminate\Database\Eloquent\Model
    {
        $user = User::create([
            'name' => $data['contact_name'] ?? $data['name'] ?? 'Venue',
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'venue',
        ]);

        return $user;
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $e) {
            $this->getRateLimitedNotification($e)?->send();

            return null;
        }

        $this->wrapInDatabaseTransaction(function (): void {
            $this->callHook('beforeValidate');
            $data = $this->form->getState();
            $this->callHook('afterValidate');
            $data = $this->mutateFormDataBeforeRegister($data);
            $this->callHook('beforeRegister');
            $user = $this->handleRegistration($data);
            $this->callHook('afterRegister');

            $venue = Venue::create([
                'user_id' => $user->id,
                'business_name' => $data['business_name'],
                'contact_name' => $data['contact_name'],
                'contact_email' => $user->email,
                'contact_phone' => $data['contact_phone'] ?? '',
                'status' => 'pending',
            ]);

            SendVenueApprovalEmail::dispatch($venue);
            DebugLogService::logVenueApprovalQueued($venue);
        });

        return app(VenueRegistrationResponse::class);
    }
}
