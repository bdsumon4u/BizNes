<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
use Filament\Forms\Components\TextInput\Actions\ShowPasswordAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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

        Model::unguard();
        Table::$defaultCurrency = 'bdt';
        Table::$defaultDateDisplayFormat = 'd-M-Y';
        Table::$defaultTimeDisplayFormat = 'h:i:s A';
        Table::$defaultDateTimeDisplayFormat = 'd-M-Y h:i:s A';

        ShowPasswordAction::configureUsing(function (ShowPasswordAction $action) {
            return $action->extraAttributes(['tabindex' => '-1']);
        });
        HidePasswordAction::configureUsing(function (HidePasswordAction $action) {
            return $action->extraAttributes(['tabindex' => '-1']);
        });
    }
}
