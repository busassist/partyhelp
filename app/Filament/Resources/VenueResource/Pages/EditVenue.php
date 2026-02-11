<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\VenueResource;
use App\Jobs\SendVenueSetPasswordEmail;
use App\Models\Venue;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditVenue extends EditRecord
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Venue $venue */
        $venue = $this->getRecord();

        return [
            Actions\Action::make('sendSetPasswordEmail')
                ->label('Send set-password email')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->visible(fn () => $venue->user_id)
                ->action(function () use ($venue) {
                    SendVenueSetPasswordEmail::dispatch($venue);
                    Notification::make()
                        ->title('Set-password email queued')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('setTemporaryPassword')
                ->label('Set temporary password')
                ->icon('heroicon-o-key')
                ->color('gray')
                ->visible(fn () => $venue->user_id)
                ->form([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->label('Temporary password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->minLength(8),
                ])
                ->action(function (array $data) use ($venue) {
                    $venue->user->update(['password' => Hash::make($data['password'])]);
                    Notification::make()
                        ->title('Temporary password set. Share it with the venue securely.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('save')
                ->label('Save changes')
                ->action(fn () => $this->save())
                ->keyBindings(['mod+s']),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
            Actions\DeleteAction::make()
                ->modalHeading('Delete venue')
                ->modalDescription('Are you sure you want to delete this venue? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete it'),
        ];
    }
}
