<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureResource\Pages;
use App\Models\Feature;
use Filament\Forms;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Room Features';

    protected static ?string $modelLabel = 'Room Feature';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('key')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true)
                ->placeholder('e.g. av_equipment')
                ->helperText('Unique slug used in room forms. Use lowercase with underscores.'),
            Forms\Components\TextInput::make('label')
                ->required()
                ->maxLength(100)
                ->placeholder('e.g. AV Equipment'),
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
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
