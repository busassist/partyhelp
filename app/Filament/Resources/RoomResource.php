<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Forms\Components\MediaLibraryPicker;
use App\Models\Room;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|\UnitEnum|null $navigationGroup = 'Venues';

    protected static ?string $modelLabel = 'Room';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('venue_id')
                ->relationship('venue', 'business_name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('style')
                ->options(config('partyhelp.room_styles'))
                ->required(),
            Grid::make(3)->schema([
                Forms\Components\TextInput::make('min_capacity')
                    ->numeric()
                    ->required()
                    ->default(10),
                Forms\Components\TextInput::make('max_capacity')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('seated_capacity')
                    ->numeric(),
            ]),
            Grid::make(2)->schema([
                Forms\Components\TextInput::make('hire_cost_min')
                    ->numeric()
                    ->prefix('$')
                    ->label('Min hire cost'),
                Forms\Components\TextInput::make('hire_cost_max')
                    ->numeric()
                    ->prefix('$')
                    ->label('Max hire cost'),
            ]),
            Forms\Components\Textarea::make('description')
                ->maxLength(1000)
                ->columnSpanFull(),
            Forms\Components\CheckboxList::make('features')
                ->options(\App\Models\Feature::options())
                ->columns(2)
                ->validationMessages(['in' => 'The selected features are invalid.']),
            MediaLibraryPicker::make('images')
                ->venueId(fn ($get, ?Room $record) => $record?->venue_id ?? $get('venue_id'))
                ->isAdmin(true)
                ->maxFiles(4),
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
                Tables\Columns\TextColumn::make('venue.business_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('style')
                    ->formatStateUsing(
                        fn (string $state) => config("partyhelp.room_styles.{$state}", $state)
                    ),
                Tables\Columns\TextColumn::make('max_capacity')->label('Capacity'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
