<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Business;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::all()->each(function (Business $business) {
            Attribute::factory(15)
                ->hasOptions(5)
                ->for($business)
                ->create();
        });
    }
}
