<?php

namespace App\Filament\Venue\Resources;

use App\Filament\Venue\Resources\MyRoomResource\Pages;
use App\Forms\Components\MediaLibraryPicker;
use App\Models\Room;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyRoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'My Rooms';

    protected static ?string $modelLabel = 'Room';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()->maxLength(255),
            Forms\Components\Select::make('style')
                ->options(config('partyhelp.room_styles'))
                ->required(),
            Grid::make(3)->schema([
                Forms\Components\TextInput::make('min_capacity')
                    ->numeric()->required()->default(10),
                Forms\Components\TextInput::make('max_capacity')
                    ->numeric()->required(),
                Forms\Components\TextInput::make('seated_capacity')
                    ->numeric(),
            ]),
            Grid::make(2)->schema([
                Forms\Components\TextInput::make('hire_cost_min')
                    ->numeric()->prefix('$')->label('Min hire cost'),
                Forms\Components\TextInput::make('hire_cost_max')
                    ->numeric()->prefix('$')->label('Max hire cost'),
            ]),
            Forms\Components\Textarea::make('description')
                ->maxLength(1000)->columnSpanFull(),
            Forms\Components\CheckboxList::make('features')
                ->options(\App\Models\Feature::options())
                ->columns(2)
                ->validationMessages(['in' => 'The selected features are invalid.']),
            MediaLibraryPicker::make('images')
                ->venueId(fn () => auth()->user()?->venue?->id)
                ->isAdmin(false)
                ->maxFiles((int) SystemSetting::get('max_photos_per_room', config('partyhelp.max_photos_per_room', 4)))
                ->autoSave(true)
                ->inlineLabel(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('style')
                    ->formatStateUsing(
                        fn (string $state) => config("partyhelp.room_styles.{$state}", $state)
                    ),
                Tables\Columns\TextColumn::make('max_capacity')
                    ->label('Capacity'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $venue = auth()->user()?->venue;

        return parent::getEloquentQuery()
            ->where('venue_id', $venue?->id ?? 0);
    }

    /** PRD 8.3: Up to 6 rooms per venue. */
    public static function canCreate(): bool
    {
        $venue = auth()->user()?->venue;
        if (! $venue) {
            return false;
        }
        $max = (int) SystemSetting::get('max_rooms_per_venue', config('partyhelp.max_rooms_per_venue', 6));

        return $venue->rooms()->count() < $max;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyRooms::route('/'),
            'create' => Pages\CreateMyRoom::route('/create'),
            'edit' => Pages\EditMyRoom::route('/{record}/edit'),
        ];
    }
}
