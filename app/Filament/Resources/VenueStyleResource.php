<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueStyleResource\Pages;
use App\Forms\Components\MediaLibraryPicker;
use App\Models\VenueStyle;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VenueStyleResource extends Resource
{
    protected static ?string $model = VenueStyle::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paint-brush';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Venue Styles';

    protected static ?string $modelLabel = 'Venue Style';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            MediaLibraryPicker::make('image_path')
                ->label('Style image')
                ->maxFiles(1)
                ->isAdmin(true)
                ->venueId(null)
                ->formatStateUsing(fn (?string $state): array => $state ? [$state] : [])
                ->dehydrateStateUsing(fn (?array $state): ?string => ! empty($state) ? $state[0] : null),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->getStateUsing(fn (VenueStyle $record) => $record->image_url)
                    ->circular(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenueStyles::route('/'),
            'create' => Pages\CreateVenueStyle::route('/create'),
            'edit' => Pages\EditVenueStyle::route('/{record}/edit'),
        ];
    }
}
