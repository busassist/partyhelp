<?php

namespace App\Filament\Resources\PricingMatrixResource\Pages;

use App\Filament\Resources\PricingMatrixResource;
use App\Models\OccasionType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListPricingMatrix extends ListRecords
{
    protected static string $resource = PricingMatrixResource::class;

    protected function getTableQuery(): Builder
    {
        return OccasionType::query()
            ->orderBy('sort_order')
            ->orderBy('label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('Occasion Type')
            ->pluralModelLabel('Occasion Types')
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Occasion Type')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                Actions\Action::make('editMatrix')
                    ->label('Edit Matrix')
                    ->url(fn (OccasionType $record): string => PricingMatrixResource::getUrl(
                        'editOccasion',
                        ['occasionType' => $record->key]
                    ))
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->defaultSort('sort_order');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
