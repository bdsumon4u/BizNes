<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ShieldSeeder::class);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ]);
        // temporary: get session team_id for restore at end
        $session_team_id = getPermissionsTeamId();
        // set actual new team_id to package instance
        $studio = $user->studios()->create([
            'name' => 'Test Studio',
            'uuid' => Str::uuid(),
        ]);
        setPermissionsTeamId($studio);
        // get the admin user and assign roles/permissions on new team model
        $user->assignRole(Utils::getSuperAdminName());
        // restore session team_id to package instance using temporary value stored above
        setPermissionsTeamId($session_team_id);

        Admin::query()->create([
            'name' => 'aDmiN User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
    }
}
