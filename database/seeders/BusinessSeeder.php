<?php

namespace Database\Seeders;

use App\Enum\LocationType;
use App\Models\Business;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::factory(10)
            ->hasAttached(User::first(), ['is_owner' => true])
            ->hasAttached(User::factory(), ['is_owner' => true])
            ->hasAttached(
                $users = User::factory(5)->create(),
                fn () => ['is_owner' => Arr::random([true, false])]
            )
            ->has(
                Location::factory(state: [
                    'name' => __('Main'),
                    'type' => LocationType::HYBRID,
                    'is_main' => true,
                ])->hasAttached($users->shuffle()->take(3))
            )
            ->has(Location::factory(2)->hasAttached($users->shuffle()->take(3)))
            ->create()
            ->each(function (Business $business) {
                foreach ($business->owners()->with('roles')->get() as $owner) {
                    $business->giveSuperAdminRoleTo($owner);
                }
            });
    }
}
