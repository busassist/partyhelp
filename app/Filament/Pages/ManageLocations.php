<?php

namespace App\Filament\Pages;

use App\Filament\Livewire\AreasTable;
use App\Filament\Livewire\PostcodesTable;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ManageLocations extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Manage Locations';

    protected static ?string $title = 'Manage Locations';

    protected static ?string $slug = 'locations';

    protected static bool $shouldRegisterNavigation = true;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Locations')
                    ->tabs([
                        Tab::make('Locations')
                            ->schema([
                                Livewire::make(AreasTable::class),
                            ]),
                        Tab::make('Postcodes')
                            ->schema([
                                Livewire::make(PostcodesTable::class),
                            ]),
                    ]),
            ]);
    }
}
