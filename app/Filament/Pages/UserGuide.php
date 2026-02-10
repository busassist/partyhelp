<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UserGuide extends Page
{
    protected static ?string $slug = 'user-guide';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'User Guide';

    protected string $view = 'admin.user-guide';

    public function hasLogo(): bool
    {
        return false;
    }
}
