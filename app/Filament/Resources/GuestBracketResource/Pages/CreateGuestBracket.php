<?php

namespace App\Filament\Resources\GuestBracketResource\Pages;

use App\Filament\Resources\GuestBracketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuestBracket extends CreateRecord
{
    protected static string $resource = GuestBracketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $state = $this->form->getState();
        if (! empty($state['is_maximum_limit'])) {
            $data['guest_max'] = null;
        }
        unset($data['is_maximum_limit']);

        return $data;
    }
}
