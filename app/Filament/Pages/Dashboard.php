<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * Single column so the Welcome (Account) widget spans full width.
     */
    public function getColumns(): int | array
    {
        return 1;
    }
}
