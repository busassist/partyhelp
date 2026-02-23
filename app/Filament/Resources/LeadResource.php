<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'Leads';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Customer Details')->schema([
                Forms\Components\TextInput::make('first_name')->required(),
                Forms\Components\TextInput::make('last_name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone')->required(),
                Forms\Components\TextInput::make('referring_domain')
                    ->label('Referring domain')
                    ->placeholder('From page URL'),
            ])->columns(2),

            Section::make('Event Details')->schema([
                Forms\Components\Select::make('occasion_type')
                    ->options(\App\Models\OccasionType::options())
                    ->required(),
                Forms\Components\TextInput::make('guest_count')
                    ->numeric()->minValue(10)->maxValue(500)->required(),
                Forms\Components\DatePicker::make('preferred_date')->required(),
                Forms\Components\Placeholder::make('location_hierarchy')
                    ->label('Preferred locations (from form)')
                    ->content(function (Lead $record) {
                        $lines = $record->location_hierarchy_lines;
                        if ($lines !== []) {
                            return new \Illuminate\Support\HtmlString(
                                '<ul class="list-disc list-inside space-y-1">' .
                                implode('', array_map(fn (string $line) => '<li>' . e($line) . '</li>', $lines)) .
                                '</ul>'
                            );
                        }

                        return $record->suburb ? 'Location → ' . e($record->suburb) : '—';
                    })
                    ->visible(fn (Lead $record): bool => $record->location_hierarchy_lines !== [] || (string) $record->suburb !== ''),
                Forms\Components\TextInput::make('suburb')
                    ->label('Primary suburb (for matching)')
                    ->required(),
                Forms\Components\Select::make('room_styles')
                    ->multiple()
                    ->options(config('partyhelp.room_styles'))
                    ->required(),
                Forms\Components\TextInput::make('budget_range'),
                Forms\Components\Textarea::make('special_requirements')
                    ->maxLength(500),
            ])->columns(2),

            Section::make('Lead Status')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'distributed' => 'Distributed',
                        'partially_fulfilled' => 'Partially Fulfilled',
                        'fulfilled' => 'Fulfilled',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])->required(),
                Forms\Components\TextInput::make('base_price')
                    ->numeric()->prefix('$'),
                Forms\Components\TextInput::make('current_price')
                    ->numeric()->prefix('$'),
                Forms\Components\TextInput::make('discount_percent')
                    ->numeric()->suffix('%'),
                Forms\Components\TextInput::make('purchase_count')
                    ->numeric()->disabled(),
                Forms\Components\TextInput::make('purchase_target')
                    ->numeric(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Customer')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('occasion_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('guest_count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('suburb')
                    ->searchable(),
                Tables\Columns\TextColumn::make('referring_domain')
                    ->label('Referring domain')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_price')
                    ->money('AUD')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'new' => 'gray',
                        'distributed' => 'info',
                        'partially_fulfilled' => 'warning',
                        'fulfilled' => 'success',
                        'expired' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('purchase_count')
                    ->label('Purchases'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'distributed' => 'Distributed',
                        'partially_fulfilled' => 'Partially Fulfilled',
                        'fulfilled' => 'Fulfilled',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('occasion_type')
                    ->options(\App\Models\OccasionType::options()),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
