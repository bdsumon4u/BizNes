<?php

namespace App\Filament\Clusters\Tenancy;

use App\Filament\Pages\Tenancy\ProfilePage;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Concerns\HasRoutes;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

use function Filament\authorize;

class BusinessSettings extends Cluster
{
    use HasRoutes;
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getLabel(): string
    {
        return 'Business Settings';
    }

    public static function getSlug(): string
    {
        return 'settings';
    }

    public static function getRelativeRouteName(): string
    {
        return 'profile';
    }

    public static function getRouteName(?string $panel = null): string
    {
        $panel = $panel ? Filament::getPanel($panel) : Filament::getCurrentPanel();

        return $panel->generateRouteName('tenant.'.static::getRelativeRouteName());
    }

    public function getView(): string
    {
        return (string) ProfilePage::$view;
    }

    public static function canView(Model $tenant): bool
    {
        try {
            return authorize('update', $tenant)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }
}
