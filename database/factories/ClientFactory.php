<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'surname' => fake()->lastName,
        'name' => fake()->firstName,
        'passport' => fake()->unique()->numerify('##########'),
        'file_passport' => fake()->imageUrl(200, 200),
        'gender' => fake()->randomElement([0, 1]), 
        'workplace' => fake()->company,
        'phone' => fake()->phoneNumber,
        'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
