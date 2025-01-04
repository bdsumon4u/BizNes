<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ])->studios()->create([
            'name' => 'Test Studio',
            'uuid' => Str::uuid(),
        ]);

        Admin::query()->create([
            'name' => 'aDmiN User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
    }
}
