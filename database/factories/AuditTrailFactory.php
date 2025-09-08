<?php

namespace Database\Factories;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditTrail>
 */
class AuditTrailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuditTrail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modules = ['Customer', 'Driver', 'Truck', 'ATC', 'Payment', 'Transaction', 'User'];
        $actions = ['create', 'update', 'delete', 'view', 'export', 'import'];
        
        return [
            'uuid' => fake()->uuid(),
            'action' => fake()->randomElement($actions),
            'module' => fake()->randomElement($modules),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}