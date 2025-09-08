<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\UserRolesAndPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        
        User::factory()->create([
            'name' => 'Accountant User',
            'email' => 'accountant@example.com',
            'role' => 'accountant',
            'password' => Hash::make('password'),
        ]);
        
        User::factory()->create([
            'name' => 'Operations Manager',
            'email' => 'operations@example.com',
            'role' => 'operations_manager',
            'password' => Hash::make('password'),
        ]);
        
        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role'=> 'staff',
            'password' => Hash::make('password'),
        ]);
        
        // Create 5 random users with random roles
        User::factory()
            ->count(5)
            ->create();
    }
}