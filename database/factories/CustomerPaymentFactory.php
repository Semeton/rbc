<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerPayment>
 */
class CustomerPaymentFactory extends Factory
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
            'payment_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'bank_name' => fake()->randomElement(['Bank of Ghana', 'Ecobank', 'Fidelity Bank', 'GCB Bank', null]),
            'notes' => fake()->optional(0.7)->paragraph(),
        ];
    }
}