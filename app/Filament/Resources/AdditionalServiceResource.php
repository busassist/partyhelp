<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdditionalServiceResource\Pages;
use App\Forms\Components\MediaLibraryPicker;
use App\Models\AdditionalService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AdditionalServiceResource extends Resource
{
    protected static ?string $model = AdditionalService::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Additional Services';

    protected static ?string $modelLabel = 'Additional Service';

    protected static ?string $pluralModelLabel = 'Additional Services';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g. Donut Wall'),
            MediaLibraryPicker::make('thumbnail_path')
                ->label('Thumbnail image')
                ->maxFiles(1)
                ->isAdmin(true)
                ->venueId(null)
                ->helperText('Shown in the additional services email and on the selection page. Use Choose / Upload images to pick from the media library.')
                ->formatStateUsing(fn (?string $state): array => $state ? [$state] : [])
                ->dehydrateStateUsing(fn (?array $state): ?string => ! empty($state) ? $state[0] : null),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->helperText('Order in which services appear.'),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->getStateUsing(fn (AdditionalService $r) => $r->thumbnail_url)
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
            'index' => Pages\ListAdditionalServices::route('/'),
            'create' => Pages\CreateAdditionalService::route('/create'),
            'edit' => Pages\EditAdditionalService::route('/{record}/edit'),
        ];
    }
}
