<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Concerns\HasRoutes;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

use function Filament\authorize;

class BusinessProfile extends Cluster
{
    use HasRoutes, InteractsWithForms, InteractsWithFormActions;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static string $view = 'filament.pages.test-page';

    public static function getLabel(): string
    {
        return 'Business Profile ds';
    }

    public static function getRelativeRouteName(): string
    {
        return 'profile';
    }

    public static function getRouteName(?string $panel = null): string
    {
        $panel = $panel ? Filament::getPanel($panel) : Filament::getCurrentPanel();

        return $panel->generateRouteName('tenant.' . static::getRelativeRouteName());
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
