<?php

namespace App\Filament\Venue\Pages;

use App\Livewire\Venue\BillingOverview;
use App\Livewire\Venue\BillingBuyCredits;
use App\Livewire\Venue\BillingPaymentMethods;
use App\Livewire\Venue\BillingSuccessBanner;
use App\Livewire\Venue\BillingTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
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

    protected static ?int $navigationSort = 99;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Livewire::make(BillingSuccessBanner::class),
                Tabs::make('Billing')
                    ->contained()
                    ->tabs([
                        Tab::make('Overview')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Livewire::make(BillingOverview::class),
                                    ]),
                            ]),
                        Tab::make('Buy Credits')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Livewire::make(BillingBuyCredits::class),
                                    ]),
                            ]),
                        Tab::make('Payment Methods')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Livewire::make(BillingPaymentMethods::class),
                                    ]),
                            ]),
                        Tab::make('View Transactions')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Livewire::make(BillingTransactions::class),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
