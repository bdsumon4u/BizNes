<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::all()->each(function (Business $business) {
            Supplier::factory(15)->for($business)->create();
        });
    }
}
