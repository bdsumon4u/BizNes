<?php

namespace App\Filament\Pages\Tenancy;

use App\Enum\LocationType;
use App\Models\Business;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Arr;

class RegisterBusiness extends RegisterTenant
{
    use BusinessForm;

    protected ?bool $hasDatabaseTransactions = true;

    /**
     * @var view-string
     */
    protected static string $view = 'filament.pages.tenancy.register-business';

    protected ?string $maxWidth = 'full';

    public static function getLabel(): string
    {
        return 'Register Business';
    }

    protected function handleRegistration(array $data): Business
    {
        $location = ['street', 'district', 'city'];
        $business = Business::query()->create(Arr::except($data, $location));
        $business->users()->attach(Filament::auth()->user(), ['is_owner' => true]);
        $business->locations()->create(Arr::only($data, $location) + [
            'name' => __('Main'),
            'type' => LocationType::HYBRID,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'is_main' => true,
        ]);

        tap(Filament::auth()->user(), fn (User $user) => $user->assignRole(
            $business->giveSuperAdminRoleTo($user)
        ));

        return $business;
    }
}
