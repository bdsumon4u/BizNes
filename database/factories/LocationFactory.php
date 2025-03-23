<?php

namespace Database\Factories;

use App\Enum\LocationType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fn (array $attributes) => $attributes['city'],
            'type' => Arr::random(LocationType::cases()),
            'street' => fake()->streetAddress(),
            'district' => fake()->state,
            'city' => fake()->city(),
        ];
    }
}
