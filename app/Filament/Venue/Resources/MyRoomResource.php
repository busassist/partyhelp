<?php

namespace App\Filament\Venue\Resources;

use App\Filament\Venue\Resources\MyRoomResource\Pages;
use App\Models\Room;
use Filament\Forms;
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
            Forms\Components\TextInput::make('min_capacity')
                ->numeric()->required()->default(10),
            Forms\Components\TextInput::make('max_capacity')
                ->numeric()->required(),
            Forms\Components\TextInput::make('seated_capacity')
                ->numeric(),
            Forms\Components\TextInput::make('hire_cost_min')
                ->numeric()->prefix('$')->label('Min hire cost'),
            Forms\Components\TextInput::make('hire_cost_max')
                ->numeric()->prefix('$')->label('Max hire cost'),
            Forms\Components\Textarea::make('description')
                ->maxLength(1000)->columnSpanFull(),
            Forms\Components\CheckboxList::make('features')
                ->options([
                    'av_equipment' => 'AV Equipment',
                    'dance_floor' => 'Dance Floor',
                    'private_bar' => 'Private Bar',
                    'outdoor_access' => 'Outdoor Access',
                    'stage' => 'Stage',
                    'projector' => 'Projector',
                    'sound_system' => 'Sound System',
                    'catering' => 'In-house Catering',
                ])->columns(2),
            Forms\Components\FileUpload::make('images')
                ->multiple()
                ->image()
                ->maxFiles(4)
                ->maxSize(5120)
                ->directory('rooms')
                ->reorderable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyRooms::route('/'),
            'create' => Pages\CreateMyRoom::route('/create'),
            'edit' => Pages\EditMyRoom::route('/{record}/edit'),
        ];
    }
}
