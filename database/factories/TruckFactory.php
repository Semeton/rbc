<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Truck>
 */
class TruckFactory extends Factory
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
            'cab_number' => 'CAB-' . fake()->unique()->numberBetween(1000, 9999),
            'registration_number' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{3}-[A-Z]{2}'),
            'truck_model' => fake()->randomElement(['Volvo FH16', 'Mercedes-Benz Actros', 'Scania R Series', 'MAN TGX', 'DAF XF']),
            'year_of_manufacture' => fake()->numberBetween(2015, 2023),
            'status' => true,
        ];
    }
}