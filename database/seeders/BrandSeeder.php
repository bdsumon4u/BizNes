<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::all()->each(function (Business $business) {
            Brand::factory(15)->for($business)->create();
        });
    }
}
