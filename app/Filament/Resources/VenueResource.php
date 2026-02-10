<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Models\Venue;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'Venues';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Business Details')->schema([
                Forms\Components\TextInput::make('business_name')->required(),
                Forms\Components\TextInput::make('abn')->label('ABN'),
                Forms\Components\TextInput::make('contact_name')->required(),
                Forms\Components\TextInput::make('contact_email')->email()->required(),
                Forms\Components\TextInput::make('contact_phone')->required(),
                Forms\Components\TextInput::make('website')->url(),
            ])->columns(2),

            Section::make('Location')->schema([
                Forms\Components\TextInput::make('address')->required(),
                Forms\Components\TextInput::make('suburb')->required(),
                Forms\Components\TextInput::make('state')->default('VIC'),
                Forms\Components\TextInput::make('postcode')->required(),
                Forms\Components\Select::make('area_id')
                    ->relationship('area', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Location (Area)'),
                Forms\Components\TagsInput::make('suburb_tags')
                    ->label('Adjacent suburbs'),
                Forms\Components\Select::make('occasion_tags')
                    ->multiple()
                    ->options(\App\Models\OccasionType::options())
                    ->label('Occasion types served'),
                Forms\Components\Select::make('venueStyles')
                    ->relationship('venueStyles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Venue styles'),
            ])->columns(2),

            Section::make('Account')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending Approval',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])->required(),
                Forms\Components\TextInput::make('credit_balance')
                    ->numeric()->prefix('$')->disabled(),
                Forms\Components\Toggle::make('auto_topup_enabled'),
                Forms\Components\TextInput::make('auto_topup_threshold')
                    ->numeric()->prefix('$'),
                Forms\Components\TextInput::make('auto_topup_amount')
                    ->numeric()->prefix('$'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business_name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('area.name')
                    ->label('Location')
                    ->sortable(),
                Tables\Columns\TextColumn::make('venueStyles.name')
                    ->label('Styles')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('contact_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('suburb')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('credit_balance')
                    ->money('AUD')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('last_activity_at')
                    ->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Venue $venue) => $venue->status === 'pending')
                    ->action(fn (Venue $venue) => $venue->update([
                        'status' => 'active',
                        'approved_at' => now(),
                    ])),
                Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Venue $venue) => $venue->status === 'active')
                    ->action(fn (Venue $venue) => $venue->update([
                        'status' => 'suspended',
                    ])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\VenueResource\RelationManagers\RoomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
