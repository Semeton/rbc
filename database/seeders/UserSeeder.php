<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\UserRolesAndPermission;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use UserRolesAndPermission;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@quarryquarto.com',
            'role' => 'admin',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Accountant User',
            'email' => 'accountant@example.com',
            'role' => 'accountant',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Operations Manager',
            'email' => 'operations@example.com',
            'role' => 'operations_manager',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'password' => 'password',
        ]);

        // Create 5 random users with random roles
        User::factory()
            ->count(5)
            ->create();
    }
}
