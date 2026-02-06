<?php

namespace App\Filament\Resources\PricingMatrixResource\Pages;

use App\Filament\Resources\PricingMatrixResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPricingMatrix extends EditRecord
{
    protected static string $resource = PricingMatrixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
