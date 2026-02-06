<?php

namespace App\Filament\Venue\Widgets;

use App\Models\LeadMatch;
use App\Models\LeadPurchase;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VenueStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $venue = auth()->user()?->venue;

        if (! $venue) {
            return [];
        }

        $available = LeadMatch::where('venue_id', $venue->id)
            ->where('status', 'notified')
            ->whereHas('lead', fn ($q) => $q->whereIn('status', ['distributed', 'partially_fulfilled'])
                ->where('expires_at', '>', now()))
            ->count();

        $purchased = LeadPurchase::where('venue_id', $venue->id)
            ->whereMonth('created_at', now()->month)
            ->count();

        $spent = LeadPurchase::where('venue_id', $venue->id)
            ->whereMonth('created_at', now()->month)
            ->sum('amount_paid');

        return [
            Stat::make('Credit Balance', '$' . number_format($venue->credit_balance, 2))
                ->color($venue->credit_balance < 50 ? 'danger' : 'success'),

            Stat::make('Available Leads', $available)
                ->description('Matching your profile')
                ->color('info'),

            Stat::make('Purchased This Month', $purchased)
                ->color('primary'),

            Stat::make('Spent This Month', '$' . number_format($spent, 2))
                ->color('warning'),
        ];
    }
}
