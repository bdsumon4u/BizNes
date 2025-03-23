<?php

namespace Database\Factories;

use App\Enum\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(mt_rand(3, 5), true),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'type' => Arr::random(ProductType::cases()),
        ];
    }
}
