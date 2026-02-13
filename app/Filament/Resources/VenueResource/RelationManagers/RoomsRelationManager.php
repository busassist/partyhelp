<?php

namespace App\Filament\Resources\VenueResource\RelationManagers;

use App\Forms\Components\MediaLibraryPicker;
use App\Models\SystemSetting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $title = 'Rooms';

    public function form(Schema $schema): Schema
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
                ->options(\App\Models\Feature::options())
                ->columns(2)
                ->validationMessages(['in' => 'The selected features are invalid.']),
            MediaLibraryPicker::make('images')
                ->venueId(fn () => $this->getOwnerRecord()?->id)
                ->isAdmin(true)
                ->maxFiles((int) SystemSetting::get('max_photos_per_room', config('partyhelp.max_photos_per_room', 4))),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('style')
                    ->formatStateUsing(
                        fn (string $state) => config("partyhelp.room_styles.{$state}", $state)
                    ),
                Tables\Columns\TextColumn::make('max_capacity')->label('Capacity'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['venue_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record) => \App\Filament\Resources\RoomResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
