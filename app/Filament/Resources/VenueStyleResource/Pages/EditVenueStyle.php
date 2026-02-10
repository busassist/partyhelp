<?php

namespace App\Filament\Resources\VenueStyleResource\Pages;

use App\Filament\Resources\VenueStyleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenueStyle extends EditRecord
{
    protected static string $resource = VenueStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
