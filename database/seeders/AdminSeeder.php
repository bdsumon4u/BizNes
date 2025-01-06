<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // temporary: get session team_id for restore at end
            $session_team_id = getPermissionsTeamId();
            // set actual new team_id to package instance
            setPermissionsTeamId(0);
            // get the admin user and assign roles/permissions on new team model
            Admin::firstOrCreate([
                'email' => 'admin@admin.com',
            ], [
                'name' => 'aDmiN',
                'password' => bcrypt('password'),
            ])->assignRole($this->getRole());
            // restore session team_id to package instance using temporary value stored above
            setPermissionsTeamId($session_team_id);
        });
    }

    private function getRole(): Role
    {
        return tap(Utils::getRoleModel()::firstOrCreate(
            [
                'name' => Utils::getSuperAdminName(),
                Utils::getTenantModelForeignKey() => 0,
            ],
            ['guard_name' => 'admin']
        ), fn (Role $role) => $role->givePermissionTo($this->getPermissions()));
    }

    private function getPermissions(): array
    {
        $permissions = ['view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role'];

        return collect($permissions)->each(function ($permission) {
            Utils::getPermissionModel()::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        })->toArray();
    }
}
