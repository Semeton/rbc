<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TruckMaintenanceRecord>
 */
class TruckMaintenanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maintenanceTypes = [
            'Engine Oil Change',
            'Brake System Repair',
            'Tire Replacement',
            'Transmission Service',
            'Electrical System Repair',
            'Cooling System Service',
            'Suspension Repair',
            'Exhaust System Repair',
        ];

        return [
            'truck_id' => \App\Models\Truck::factory(),
            'description' => fake()->randomElement($maintenanceTypes).' - '.fake()->sentence(),
            'cost_of_maintenance' => fake()->randomFloat(2, 500, 5000),
            'status' => true,
        ];
    }
}
