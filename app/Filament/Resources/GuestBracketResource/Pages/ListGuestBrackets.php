<?php

namespace App\Filament\Resources\GuestBracketResource\Pages;

use App\Filament\Resources\GuestBracketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuestBrackets extends ListRecords
{
    protected static string $resource = GuestBracketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
