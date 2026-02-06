<?php

namespace App\Filament\Venue\Resources\MyRoomResource\Pages;

use App\Filament\Venue\Resources\MyRoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyRoom extends CreateRecord
{
    protected static string $resource = MyRoomResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['venue_id'] = auth()->user()->venue->id;

        return $data;
    }
}
