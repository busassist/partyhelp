<?php

namespace App\Providers;

use App\Filament\Livewire\AreasTable;
use App\Filament\Livewire\PostcodesTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('areas-table', AreasTable::class);
        Livewire::component('postcodes-table', PostcodesTable::class);
    }
}
