<?php

namespace App\Filament\Resources\BudgetRangeResource\Pages;

use App\Filament\Resources\BudgetRangeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudgetRange extends EditRecord
{
    protected static string $resource = BudgetRangeResource::class;

    public function mutateFormDataBeforeFill(array $data): array
    {
        $data['is_maximum'] = $this->record->max_value === null;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
