<?php

namespace App\Providers\Filament;

use App\Filament\Clusters\Tenancy\BusinessSettings;
use App\Filament\Pages\PointOfSale;
use App\Filament\Pages\Tenancy\RegisterBusiness;
use App\Http\Middleware\SetTenantConfig;
use App\Models\Business;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Filament\Facades\Filament;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
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
            ->font('Roboto', provider: GoogleFontProvider::class)
            ->brandLogo(fn () => Filament::getTenant()?->getFilamentLogoUrl())
            ->favicon(fn () => Filament::getTenant()?->getFilamentAvatarUrl())
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling(null)
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
                SetTenantConfig::class,
            ])
            ->tenantMiddleware([
                SyncShieldTenant::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
                EnsureEmailIsVerified::class,
            ])
            ->tenant(Business::class, 'domain')
            ->tenantDomain('{tenant:domain}')
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

    public function boot(): void
    {
        Model::resolveRelationUsing('business', fn (Model $model) => $model->belongsTo(Business::class, (new Business)->getForeignKey()));

        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            fn () => Blade::render('<a
                href="{{ url(\'/\') }}"
                target="_blank"
                class="visit-site"
            >
                <x-untitledui-google-chrome class="size-6" stroke-width="1.5" aria-hidden="true" />
            </a>'),
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn () => Blade::render('<a
                href="'.PointOfSale::getUrl().'"
                class="point-of-sale"
                wire:navigate
                title="PoS"
            >
                <x-icon name="'.PointOfSale::getNavigationIcon().'" class="size-6" stroke-width="1.5" aria-hidden="true" />
            </a>'),
        );
    }
}
