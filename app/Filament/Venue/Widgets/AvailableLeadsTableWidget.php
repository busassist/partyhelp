<?php

namespace App\Filament\Venue\Widgets;

use App\Filament\Venue\Resources\AvailableLeadResource;
use App\Models\LeadMatch;
use App\Services\LeadPurchaseService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;

class AvailableLeadsTableWidget extends BaseTableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Available Leads';
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Actions\Action::make('viewAll')
                ->label('View all')
                ->url(AvailableLeadResource::getUrl('index'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray'),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        $venue = auth()->user()?->venue;

        if (! $venue) {
            return LeadMatch::query()->whereRaw('1 = 0');
        }

        return LeadMatch::query()
            ->where('venue_id', $venue->id)
            ->where('status', 'notified')
            ->whereHas('lead', function (Builder $query) {
                $query->whereIn('status', ['distributed', 'partially_fulfilled'])
                    ->where('expires_at', '>', now());
            })
            ->with('lead');
    }

    public function table(Table $table): Table
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
            ->recordActions([
                Actions\Action::make('purchase')
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
            ->defaultSort('match_score', 'desc')
            ->paginated([5, 10, 25]);
    }

    public static function canView(): bool
    {
        return (bool) auth()->user()?->venue;
    }
}
