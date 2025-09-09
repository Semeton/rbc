<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyTruckRecord>
 */
class DailyTruckRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'driver_id' => \App\Models\Driver::factory(),
            'truck_id' => \App\Models\Truck::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'atc_collection_date' => fake()->dateTimeBetween('-2 months', '-1 week'),
            'load_dispatch_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'fare' => fake()->randomFloat(2, 1000, 5000),
            'gas_chop_money' => fake()->randomFloat(2, 100, 500),
            'balance' => fake()->randomFloat(2, 500, 4000),
            'status' => true,
        ];
    }
}
