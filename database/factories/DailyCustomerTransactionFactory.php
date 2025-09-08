<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyCustomerTransaction>
 */
class DailyCustomerTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'date' => fake()->dateTimeBetween('-2 months', 'now'),
            'origin' => fake()->city(),
            'deport_details' => fake()->sentence(),
            'cement_type' => fake()->randomElement(['Type I', 'Type II', 'Type III', 'Type IV', 'Type V']),
            'destination' => fake()->city(),
            'atc_cost' => fake()->randomFloat(2, 1000, 10000),
            'transport_cost' => fake()->randomFloat(2, 500, 5000),
            'status' => true,
        ];
    }
}