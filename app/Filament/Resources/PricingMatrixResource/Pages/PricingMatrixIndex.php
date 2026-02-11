<?php

namespace App\Filament\Resources\PricingMatrixResource\Pages;

use App\Filament\Resources\PricingMatrixResource;
use App\Livewire\GuestBracketsTable;
use App\Livewire\OccasionTypesTable;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class PricingMatrixIndex extends Page
{
    protected static string $resource = PricingMatrixResource::class;

    protected string $view = 'filament-panels::pages.simple';

    public function getTitle(): string | Htmlable
    {
        return 'Pricing Matrix';
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Pricing')
                    ->contained()
                    ->tabs([
                        Tab::make('Occasion Types')
                            ->schema([
                                Livewire::make(OccasionTypesTable::class),
                            ]),
                        Tab::make('Guest Brackets')
                            ->schema([
                                Livewire::make(GuestBracketsTable::class),
                            ]),
                    ]),
            ]);
    }
}
