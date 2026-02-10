<?php

namespace App\Filament\Venue\Pages;

use App\Livewire\Venue\BillingOverview;
use App\Livewire\Venue\BillingBuyCredits;
use App\Livewire\Venue\BillingPaymentMethods;
use App\Livewire\Venue\BillingTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ManageBillingCredits extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Manage Billing & Credits';

    protected static ?string $title = 'Manage Billing & Credits';

    protected static ?string $slug = 'billing';

    protected static bool $shouldRegisterNavigation = true;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Billing')
                    ->tabs([
                        Tab::make('Overview')
                            ->schema([
                                Livewire::make(BillingOverview::class),
                            ]),
                        Tab::make('Buy Credits')
                            ->schema([
                                Livewire::make(BillingBuyCredits::class),
                            ]),
                        Tab::make('Payment Methods')
                            ->schema([
                                Livewire::make(BillingPaymentMethods::class),
                            ]),
                        Tab::make('View Transactions')
                            ->schema([
                                Livewire::make(BillingTransactions::class),
                            ]),
                    ]),
            ]);
    }
}
