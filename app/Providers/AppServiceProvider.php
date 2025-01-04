<?php

namespace App\Providers;

use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\TextInput\Actions\ShowPasswordAction;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        foreach ([ShowPasswordAction::class, HidePasswordAction::class] as $action) {
            $action::configureUsing(fn ($action) => $action->extraAttributes([
                'tabindex' => -1,
            ]));
        }
    }
}
