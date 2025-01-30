<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\Tenancy\BusinessSettings;
use App\Filament\Pages\Tenancy\RegisterBusiness;
use App\Models\Business;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class StudioPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('biz')
            ->profile()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Roboto')
            ->globalSearch()
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->tenantMiddleware([
                SyncShieldTenant::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
                EnsureEmailIsVerified::class,
            ])
            ->tenant(Business::class, 'uuid')
            ->tenantMenuItems([
                'register' => MenuItem::make()->visible(function () {
                    return optional(Filament::auth()->user())->businesses()->count() < 4;
                }),
            ])
            ->tenantRegistration(RegisterBusiness::class)
            ->tenantProfile(BusinessSettings::class)
            ->viteTheme('resources/css/filament/studio/theme.css')
            ->spa();
    }
}
