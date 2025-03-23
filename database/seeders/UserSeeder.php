<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('shield:generate --all --ignore-existing-policies --panel=app');

        User::firstOrCreate([
            'email' => 'test@test.com',
        ], [
            'name' => 'Test User',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        // User::factory(10)->hasAttached(Business::factory(), ['is_owner' => true])->create();
        // User::factory(10)->hasAttached(Business::query()->inRandomOrder()->take(3)->get())->create();
    }
}
