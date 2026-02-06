<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OccasionTypeResource\Pages;
use App\Models\OccasionType;
use Filament\Forms;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OccasionTypeResource extends Resource
{
    protected static ?string $model = OccasionType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cake';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Occasion Types';

    protected static ?string $modelLabel = 'Occasion Type';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('key')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true)
                ->placeholder('e.g. 21st_birthday')
                ->helperText('Unique slug used in forms and pricing. Use lowercase with underscores.'),
            Forms\Components\TextInput::make('label')
                ->required()
                ->maxLength(100)
                ->placeholder('e.g. 21st Birthday'),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('key')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('label')->sortable()->searchable(),
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
            'index' => Pages\ListOccasionTypes::route('/'),
            'create' => Pages\CreateOccasionType::route('/create'),
            'edit' => Pages\EditOccasionType::route('/{record}/edit'),
        ];
    }
}
