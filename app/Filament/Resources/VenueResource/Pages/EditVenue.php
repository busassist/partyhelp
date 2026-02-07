<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\VenueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenue extends EditRecord
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
