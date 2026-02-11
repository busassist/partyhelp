<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestBracketResource\Pages;
use App\Models\GuestBracket;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GuestBracketResource extends Resource
{
    protected static ?string $model = GuestBracket::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Pricing';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Guest Brackets';

    protected static ?string $modelLabel = 'Guest Bracket';

    protected static ?string $pluralModelLabel = 'Guest Brackets';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'guest-brackets';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('guest_min')
                ->numeric()->required()->minValue(0)->label('Guest count from'),
            Forms\Components\Checkbox::make('is_maximum_limit')
                ->label('Set to maximum limit (no upper bound)')
                ->helperText('When checked, this bracket means "from this number upwards" and displays as e.g. 100+.')
                ->live()
                ->dehydrated(false),
            Forms\Components\TextInput::make('guest_max')
                ->label('Guest count to')
                ->numeric()
                ->minValue(0)
                ->required(fn (Get $get): bool => ! (bool) $get('is_maximum_limit'))
                ->visible(fn (Get $get): bool => ! (bool) $get('is_maximum_limit')),
            Forms\Components\Placeholder::make('guest_max_maximum')
                ->label('Guest count to')
                ->content('Maximum')
                ->visible(fn (Get $get): bool => (bool) $get('is_maximum_limit')),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Range')
                    ->getStateUsing(function (GuestBracket $record): string {
                        if ($record->isMaximumLimit() || ($record->guest_max !== null && $record->guest_max >= 500)) {
                            return $record->guest_min . '-Maximum';
                        }
                        return $record->label;
                    }),
                Tables\Columns\TextColumn::make('guest_min')->label('From')->sortable(),
                Tables\Columns\TextColumn::make('guest_max')
                    ->label('To')
                    ->formatStateUsing(function (?int $state): string {
                        if ($state === null || $state >= 500) {
                            return 'Maximum';
                        }
                        return (string) $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
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
            'index' => Pages\ListGuestBrackets::route('/'),
            'create' => Pages\CreateGuestBracket::route('/create'),
            'edit' => Pages\EditGuestBracket::route('/{record}/edit'),
        ];
    }
}
