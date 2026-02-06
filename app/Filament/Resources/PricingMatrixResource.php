<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingMatrixResource\Pages;
use App\Models\PricingMatrix;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PricingMatrixResource extends Resource
{
    protected static ?string $model = PricingMatrix::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string | \UnitEnum | null $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Pricing Matrix';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('occasion_type')
                ->options(config('partyhelp.occasion_types'))
                ->required(),
            Forms\Components\TextInput::make('guest_min')
                ->numeric()->required()->label('Guest count from'),
            Forms\Components\TextInput::make('guest_max')
                ->numeric()->required()->label('Guest count to'),
            Forms\Components\TextInput::make('price')
                ->numeric()->prefix('$')->required(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('occasion_type')
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('guest_min')
                    ->label('From'),
                Tables\Columns\TextColumn::make('guest_max')
                    ->label('To'),
                Tables\Columns\TextColumn::make('price')
                    ->money('AUD')->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('occasion_type');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingMatrix::route('/'),
            'create' => Pages\CreatePricingMatrix::route('/create'),
            'edit' => Pages\EditPricingMatrix::route('/{record}/edit'),
        ];
    }
}
