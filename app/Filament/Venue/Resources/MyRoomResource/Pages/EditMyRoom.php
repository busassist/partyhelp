<?php

namespace App\Filament\Venue\Resources\MyRoomResource\Pages;

use App\Filament\Venue\Resources\MyRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyRoom extends EditRecord
{
    protected static string $resource = MyRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
