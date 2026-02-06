<?php

namespace App\Filament\Venue\Resources;

use App\Filament\Venue\Resources\PurchasedLeadResource\Pages;
use App\Models\LeadPurchase;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PurchasedLeadResource extends Resource
{
    protected static ?string $model = LeadPurchase::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Purchased Leads';

    protected static ?string $modelLabel = 'Purchased Lead';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Customer Details')->schema([
                Forms\Components\Placeholder::make('customer_name')
                    ->content(fn ($record) => $record->lead->full_name),
                Forms\Components\Placeholder::make('customer_email')
                    ->content(fn ($record) => $record->lead->email),
                Forms\Components\Placeholder::make('customer_phone')
                    ->content(fn ($record) => $record->lead->phone),
            ])->columns(3),

            Section::make('Event Details')->schema([
                Forms\Components\Placeholder::make('occasion')
                    ->content(fn ($record) => $record->lead->occasion_type),
                Forms\Components\Placeholder::make('guests')
                    ->content(fn ($record) => $record->lead->guest_count),
                Forms\Components\Placeholder::make('date')
                    ->content(fn ($record) => $record->lead->preferred_date->format('d M Y')),
                Forms\Components\Placeholder::make('suburb')
                    ->content(fn ($record) => $record->lead->suburb),
            ])->columns(4),

            Section::make('Follow Up')->schema([
                Forms\Components\Select::make('lead_status')
                    ->options([
                        'pending' => 'Pending',
                        'contacted' => 'Contacted',
                        'quoted' => 'Quoted',
                        'booked' => 'Booked',
                        'lost' => 'Lost',
                    ])->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lead.full_name')
                    ->label('Customer')->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('lead.occasion_type')
                    ->label('Occasion')->badge(),
                Tables\Columns\TextColumn::make('lead.guest_count')
                    ->label('Guests'),
                Tables\Columns\TextColumn::make('lead.preferred_date')
                    ->label('Date')->date(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->money('AUD'),
                Tables\Columns\TextColumn::make('lead_status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'gray',
                        'contacted' => 'info',
                        'quoted' => 'warning',
                        'booked' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Purchased')->dateTime()->sortable(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $venue = auth()->user()?->venue;

        return parent::getEloquentQuery()
            ->where('venue_id', $venue?->id ?? 0)
            ->with('lead');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchasedLeads::route('/'),
            'edit' => Pages\EditPurchasedLead::route('/{record}/edit'),
        ];
    }
}
