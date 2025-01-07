<?php

namespace Database\Seeders;

use App\Models\Admin;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('shield:generate --all --ignore-existing-policies --panel=admin');

        DB::transaction(function () {
            // temporary: get session team_id for restore at end
            $session_team_id = getPermissionsTeamId();
            // set actual new team_id to package instance
            setPermissionsTeamId(null);
            // get the admin user and assign roles/permissions on new team model
            Admin::firstOrCreate([
                'email' => 'admin@admin.com',
            ], [
                'name' => 'aDmiN',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ])->assignRole(Utils::getSuperAdminName());
            // restore session team_id to package instance using temporary value stored above
            setPermissionsTeamId($session_team_id);
        });
    }
}
