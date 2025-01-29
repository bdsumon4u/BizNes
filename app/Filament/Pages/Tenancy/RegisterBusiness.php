<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterBusiness extends RegisterTenant
{
    use BusinessForm;

    protected ?bool $hasDatabaseTransactions = true;

    /**
     * @var view-string
     */
    protected static string $layout = 'filament.components.layout.simple';

    /**
     * @var view-string
     */
    protected static string $view = 'filament.pages.tenancy.register-business';

    public static function getLabel(): string
    {
        return 'Register Business';
    }

    protected function handleRegistration(array $data): Business
    {
        $business = Business::query()->create($data);
        $business->users()->attach(Filament::auth()->user(), ['is_owner' => true]);

        // temporary: get session team_id for restore at end
        $session_team_id = getPermissionsTeamId();
        // set actual new team_id to package instance
        setPermissionsTeamId($business);
        // get the admin user and assign roles/permissions on new team model
        tap(Filament::auth()->user(), fn (User $user) => $user->assignRole(
            $this->getRole($business->id)
        ));
        // restore session team_id to package instance using temporary value stored above
        setPermissionsTeamId($session_team_id);

        return $business;
    }

    private function getRole($businessId): Role
    {
        return tap(Utils::getRoleModel()::firstOrCreate(
            [
                'name' => Utils::getSuperAdminName(),
                Utils::getTenantModelForeignKey() => $businessId,
            ],
            ['guard_name' => Utils::getFilamentAuthGuard()]
        ), fn (Role $role) => $role->givePermissionTo(
            $this->getPermissions($businessId)
        ));
    }

    private function getPermissions(): array
    {
        return Utils::getPermissionModel()::where('guard_name', Utils::getFilamentAuthGuard())->pluck('id')->toArray();
    }
}
