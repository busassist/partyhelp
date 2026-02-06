<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\LeadPurchase;
use App\Models\Venue;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Leads This Month', Lead::whereMonth('created_at', now()->month)->count())
                ->description('Total leads received')
                ->color('primary'),

            Stat::make('Leads This Week', Lead::where('created_at', '>=', now()->startOfWeek())->count())
                ->description('This week')
                ->color('info'),

            Stat::make('Fulfilled Rate', $this->getFulfilmentRate() . '%')
                ->description('Leads reaching threshold')
                ->color('success'),

            Stat::make('Revenue This Month', '$' . number_format($this->getMonthlyRevenue(), 2))
                ->description('Credit spent on leads')
                ->color('warning'),

            Stat::make('Active Venues', Venue::where('status', 'active')->count())
                ->description('Receiving opportunities')
                ->color('success'),

            Stat::make('Pending Approval', Venue::where('status', 'pending')->count())
                ->description('Venues awaiting review')
                ->color('danger'),
        ];
    }

    private function getFulfilmentRate(): string
    {
        $total = Lead::whereMonth('created_at', now()->month)
            ->whereNotIn('status', ['new'])->count();
        $fulfilled = Lead::whereMonth('created_at', now()->month)
            ->where('status', 'fulfilled')->count();

        return $total > 0 ? round(($fulfilled / $total) * 100) : '0';
    }

    private function getMonthlyRevenue(): float
    {
        return LeadPurchase::whereMonth('created_at', now()->month)
            ->sum('amount_paid');
    }
}
