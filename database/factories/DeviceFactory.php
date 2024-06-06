<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'imei' => fake()->unique()->randomNumber(9),
        'model' => fake()->randomElement(['Apple', 'Samsung', 'RedMI']),
        'provider' => fake()->company,
        'account' => fake()->userName,
        'status' => fake()->randomElement([0, 1]),
        'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
