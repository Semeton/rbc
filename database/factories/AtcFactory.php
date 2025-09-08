<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Atc>
 */
class AtcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'atc_number' => fake()->unique()->numberBetween(10000, 99999),
            'atc_type' => fake()->randomElement(['bg', 'cash_payment']),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'tons' => fake()->randomFloat(2, 10, 100),
            'status' => true,
        ];
    }
}
