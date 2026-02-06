<?php

namespace App\Filament\Resources\OccasionTypeResource\Pages;

use App\Filament\Resources\OccasionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOccasionType extends EditRecord
{
    protected static string $resource = OccasionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
