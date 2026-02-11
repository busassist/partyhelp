<?php

namespace App\Filament\Resources\GuestBracketResource\Pages;

use App\Filament\Resources\GuestBracketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuestBracket extends EditRecord
{
    protected static string $resource = GuestBracketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['is_maximum_limit'] = ($data['guest_max'] ?? null) === null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $state = $this->form->getState();
        if (! empty($state['is_maximum_limit'])) {
            $data['guest_max'] = null;
        }
        unset($data['is_maximum_limit']);

        return $data;
    }
}
