<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetRangeResource\Pages;
use App\Models\BudgetRange;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetRangeResource extends Resource
{
    protected static ?string $model = BudgetRange::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Budget Ranges';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('label')
                ->required()->placeholder('e.g. 1500-3000'),
            Forms\Components\TextInput::make('min_value')
                ->numeric()->required()->prefix('$'),
            Forms\Components\TextInput::make('max_value')
                ->numeric()->required()->prefix('$'),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->sortable(),
                Tables\Columns\TextColumn::make('min_value')
                    ->money('AUD')->label('Min'),
                Tables\Columns\TextColumn::make('max_value')
                    ->money('AUD')->label('Max'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgetRanges::route('/'),
            'create' => Pages\CreateBudgetRange::route('/create'),
            'edit' => Pages\EditBudgetRange::route('/{record}/edit'),
        ];
    }
}
