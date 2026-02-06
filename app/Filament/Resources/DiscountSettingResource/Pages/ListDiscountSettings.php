<?php

namespace App\Filament\Resources\DiscountSettingResource\Pages;

use App\Filament\Resources\DiscountSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscountSettings extends ListRecords
{
    protected static string $resource = DiscountSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
