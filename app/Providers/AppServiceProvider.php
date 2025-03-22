<?php

namespace App\Providers;

use App\Enum\Dimension\Length;
use App\Enum\Dimension\Volume;
use App\Enum\Dimension\Weight;
use App\Models\Permission;
use App\Models\Role;
use Carbon\CarbonImmutable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
use Filament\Forms\Components\TextInput\Actions\ShowPasswordAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Number;
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

        Vite::useAggressivePrefetching();
        Date::use(CarbonImmutable::class);
        DB::prohibitDestructiveCommands(app()->isProduction());
        Model::shouldBeStrict(! app()->isProduction());
        URL::forceHttps(app()->isProduction());
        Model::unguard();
        Number::useCurrency('BDT');
        Table::$defaultCurrency = 'bdt';
        Table::$defaultDateDisplayFormat = 'd-M-Y';
        Table::$defaultTimeDisplayFormat = 'h:i:s A';
        Table::$defaultDateTimeDisplayFormat = 'd-M-Y h:i:s A';

        // Select::configureUsing(fn (Select $select) => $select->native(false));

        // CreateAction::configureUsing(fn (CreateAction $action) => $action->icon('heroicon-o-plus'));
        DeleteAction::configureUsing(fn (DeleteAction $action) => $action->icon('heroicon-o-trash'));

        ShowPasswordAction::configureUsing(function (ShowPasswordAction $action) {
            return $action->extraAttributes(['tabindex' => '-1']);
        });
        HidePasswordAction::configureUsing(function (HidePasswordAction $action) {
            return $action->extraAttributes(['tabindex' => '-1']);
        });

        $this->registerBlueprintMacros();
    }

    private function registerBlueprintMacros()
    {
        Blueprint::macro('seo_v1', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->json('metadata')->nullable();
        });

        Blueprint::macro('shipping_v1', function (Blueprint $table) {
            $table->string('weight_unit')->default(Weight::KG());
            $table->decimal('weight_value', 10)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('height_unit')->default(Length::CM());
            $table->decimal('height_value', 10)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('width_unit')->default(Length::CM());
            $table->decimal('width_value', 10)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('depth_unit')->default(Length::CM());
            $table->decimal('depth_value', 10)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('volume_unit')->default(Volume::L());
            $table->decimal('volume_value', 10)->nullable()
                ->default(0.00)
                ->unsigned();
        });

        // dropLayer_vNext
    }
}
