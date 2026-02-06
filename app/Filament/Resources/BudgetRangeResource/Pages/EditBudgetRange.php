<?php

namespace App\Filament\Resources\BudgetRangeResource\Pages;

use App\Filament\Resources\BudgetRangeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudgetRange extends EditRecord
{
    protected static string $resource = BudgetRangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
