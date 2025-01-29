<?php

namespace App\Filament\Pages\Tenancy;

use App\Filament\Clusters\BusinessProfile;
use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Panel;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class EditBusinessProfile extends EditTenantProfile
{
    use BusinessForm;
    protected static ?string $cluster = BusinessProfile::class;

    protected static ?string $slug = 'profile';

    public static function getRelativeRouteName(): string
    {
        return (string) str(static::getSlug())->replace('/', '.');
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

    protected static bool $isDiscovered = true;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected ?string $maxContentWidth = MaxWidth::ThreeExtraLarge->value;

    public static function getLabel(): string
    {
        return 'Business Profile ds';
    }
}
