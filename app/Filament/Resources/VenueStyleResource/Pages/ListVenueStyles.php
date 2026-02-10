<?php

namespace App\Filament\Resources\VenueStyleResource\Pages;

use App\Filament\Resources\VenueStyleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVenueStyles extends ListRecords
{
    protected static string $resource = VenueStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
