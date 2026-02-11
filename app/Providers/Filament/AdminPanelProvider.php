<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AdminPanelAuthenticate;
use Filament\Actions\Action;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use App\Filament\Pages\Dashboard as AppDashboard;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->userMenuItems([
                'profile' => fn (Action $action) => $action->label('Your profile'),
            ])
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::hex('#7c3aed'),
            ])
            ->brandName('Partyhelp Admin')
            ->favicon(asset('images/brand/ph-icon-dark.png'))
            ->brandLogo(asset('images/brand/ph-logo-dark.png'))
            ->darkModeBrandLogo(asset('images/brand/ph-logo-white.png'))
            ->brandLogoHeight('55px')
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn () => new HtmlString(
                view('admin.user-guide-link')->render()
            ))
            ->renderHook(PanelsRenderHook::SCRIPTS_AFTER, fn () => new HtmlString(
                '<script>document.addEventListener("livewire:init",function(){Livewire.hook("request.failed",function(e){if(!e)return;var u=e.redirect||(e.response&&e.response.redirect);if((e.status===401||e.status===403)&&u){window.location.href=u;}else if(e.status===401){window.location.href="/admin/login";}});});</script>'
            ))
            ->renderHook(PanelsRenderHook::STYLES_AFTER, function () {
                $path = resource_path('css/user-guide.css');
                $userGuideCss = is_file($path) ? file_get_contents($path) : '';

                return new HtmlString(
                    '<style>' .
                    '.fi-logo, .fi-logo img { height: 55px !important; object-fit: contain; }' .
                    '.fi-sidebar-group-label { color: #7c3aed !important; }' .
                    '.dark .fi-sidebar-group-label { color: white !important; }' .
                    '.fi-sidebar-nav { border-right: 1px solid #eee; }' .
                    '</style>' .
                    ($userGuideCss !== '' ? '<style>' . $userGuideCss . '</style>' : '')
                );
            })
            ->navigationGroups([
                'Leads',
                'Venues',
                NavigationGroup::make('Manage System Data')->collapsed(true),
                NavigationGroup::make('Manage System Pricing')->collapsed(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                AppDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
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
                AdminPanelAuthenticate::class,
            ]);
    }
}
