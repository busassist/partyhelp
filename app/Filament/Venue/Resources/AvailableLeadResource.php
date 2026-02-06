<?php

namespace App\Filament\Venue\Resources;

use App\Filament\Venue\Resources\AvailableLeadResource\Pages;
use App\Models\LeadMatch;
use App\Services\LeadPurchaseService;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvailableLeadResource extends Resource
{
    protected static ?string $model = LeadMatch::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Available Leads';

    protected static ?string $modelLabel = 'Lead Opportunity';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lead.occasion_type')
                    ->label('Occasion')->badge(),
                Tables\Columns\TextColumn::make('lead.guest_count')
                    ->label('Guests'),
                Tables\Columns\TextColumn::make('lead.suburb')
                    ->label('Suburb'),
                Tables\Columns\TextColumn::make('lead.preferred_date')
                    ->label('Date')->date(),
                Tables\Columns\TextColumn::make('lead.current_price')
                    ->label('Price')->money('AUD'),
                Tables\Columns\TextColumn::make('lead.expires_at')
                    ->label('Expires')->since(),
                Tables\Columns\TextColumn::make('match_score')
                    ->label('Match %')
                    ->formatStateUsing(fn ($state) => round($state) . '%'),
            ])
            ->actions([
                Tables\Actions\Action::make('purchase')
                    ->label('Purchase Lead')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Purchase This Lead')
                    ->modalDescription(fn (LeadMatch $record) => "This will debit \${$record->lead->current_price} from your credit balance.")
                    ->action(function (LeadMatch $record) {
                        $venue = auth()->user()->venue;
                        $service = app(LeadPurchaseService::class);
                        $result = $service->purchase($record->lead, $venue);

                        if (is_string($result)) {
                            Notification::make()
                                ->title('Purchase Failed')
                                ->body($result)
                                ->danger()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Lead Purchased!')
                                ->body('Customer details are now available in Purchased Leads.')
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->defaultSort('match_score', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $venue = auth()->user()?->venue;

        return parent::getEloquentQuery()
            ->where('venue_id', $venue?->id ?? 0)
            ->where('status', 'notified')
            ->whereHas('lead', function (Builder $query) {
                $query->whereIn('status', ['distributed', 'partially_fulfilled'])
                    ->where('expires_at', '>', now());
            })
            ->with('lead');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvailableLeads::route('/'),
        ];
    }
}
