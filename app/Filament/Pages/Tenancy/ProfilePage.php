<?php

namespace App\Filament\Pages\Tenancy;

use App\Filament\Clusters\Tenancy\BusinessSettings;
use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Panel;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Route;

class ProfilePage extends EditTenantProfile
{
    use BusinessForm;

    protected static ?string $cluster = BusinessSettings::class;

    protected static ?string $slug = 'profile';

    protected static bool $isDiscovered = true;

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $navigationIcon = 'ri-profile-line';

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? static::getLabel();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public static function registerRoutes(Panel $panel): void
    {
        if (filled(static::getCluster())) {
            Route::name(static::prependClusterRouteBaseName(''))
                ->prefix(static::prependClusterSlug(''))
                ->group(fn () => static::routes($panel));

            return;
        }

        Route::group(fn () => static::routes($panel));
    }

    public static function getRouteName(?string $panel = null): string
    {
        $panel = $panel ? Filament::getPanel($panel) : Filament::getCurrentPanel();

        $routeName = static::getRelativeRouteName();
        $routeName = static::prependClusterRouteBaseName($routeName);

        return $panel->generateRouteName($routeName);
    }

    public static function getLabel(): string
    {
        return static::getNavigationLabel();
    }

    protected function getFormActions(): array
    {
        return [
            //
        ];
    }
}
