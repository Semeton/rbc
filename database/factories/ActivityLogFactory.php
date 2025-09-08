<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Truck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loggableTypes = [Customer::class, Truck::class];
        $events = ['created', 'updated', 'deleted'];
        $selectedType = fake()->randomElement($loggableTypes);

        return [
            'loggable_type' => $selectedType,
            'loggable_id' => function () use ($selectedType) {
                if ($selectedType === Customer::class) {
                    return fake()->numberBetween(1, 5); // Assuming 5 customers
                } else {
                    return fake()->numberBetween(1, 5); // Assuming 5 trucks
                }
            },
            'event' => fake()->randomElement($events),
            'properties' => json_encode([
                'old' => [],
                'attributes' => ['updated_at' => now()->toDateTimeString()]
            ]),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
