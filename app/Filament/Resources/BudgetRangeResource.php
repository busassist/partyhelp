<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetRangeResource\Pages;
use App\Models\BudgetRange;
use Filament\Forms;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetRangeResource extends Resource
{
    protected static ?string $model = BudgetRange::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Pricing';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Budget Ranges';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('label')
                ->required()->placeholder('e.g. 1500-3000 or 5000+'),
            Forms\Components\TextInput::make('min_value')
                ->numeric()->required()->prefix('$'),
            Forms\Components\Toggle::make('is_maximum')
                ->label('No maximum (open-ended range)')
                ->helperText('When on, this range has no upper limit (e.g. 5000+).')
                ->live()
                ->default(false),
            Forms\Components\TextInput::make('max_value')
                ->numeric()->prefix('$')
                ->required(fn ($get) => ! $get('is_maximum'))
                ->hidden(fn ($get) => (bool) $get('is_maximum'))
                ->dehydrated(true)
                ->dehydrateStateUsing(fn ($state, $get) => $get('is_maximum') ? null : $state),
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
                    ->label('Max')
                    ->formatStateUsing(fn ($state) => $state === null ? '—' : '$' . number_format((float) $state, 2)),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
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
