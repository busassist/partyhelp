<?php

namespace App\Providers\Filament;

use Filament\Actions\Action;
use App\Http\Middleware\VenuePanelAuthenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class VenuePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('venue')
            ->path('venue')
            ->login()
            ->registration()
            ->profile()
            ->userMenuItems([
                'profile' => fn (Action $action) => $action->label('Your profile'),
            ])
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::hex('#7c3aed'),
            ])
            ->brandName('Partyhelp')
            ->favicon(asset('images/brand/ph-icon-dark.png'))
            ->brandLogo(asset('images/brand/ph-logo-dark.png'))
            ->darkModeBrandLogo(asset('images/brand/ph-logo-white.png'))
            ->brandLogoHeight('55px')
            ->renderHook(PanelsRenderHook::STYLES_AFTER, fn () => new HtmlString(
                '<style>' .
                '.fi-logo, .fi-logo img { height: 55px !important; object-fit: contain; }' .
                '.fi-sidebar-group-label { color: #7c3aed !important; }' .
                '.dark .fi-sidebar-group-label { color: white !important; }' .
                '.fi-sidebar-nav { border-right: 1px solid #eee; }' .
                '</style>'
            ))
            ->discoverResources(
                in: app_path('Filament/Venue/Resources'),
                for: 'App\Filament\Venue\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Venue/Pages'),
                for: 'App\Filament\Venue\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Venue/Widgets'),
                for: 'App\Filament\Venue\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                VenuePanelAuthenticate::class,
            ]);
    }
}
