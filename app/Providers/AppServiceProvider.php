<?php

namespace App\Providers;

use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
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
        foreach ([ShowPasswordAction::class, HidePasswordAction::class] as $action) {
            $action::configureUsing(fn ($action) => $action->extraAttributes([
                'tabindex' => -1,
            ]));
        }
    }
}
