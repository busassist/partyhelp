<?php

namespace App\Filament\Resources\OccasionTypeResource\Pages;

use App\Filament\Resources\OccasionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOccasionTypes extends ListRecords
{
    protected static string $resource = OccasionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
