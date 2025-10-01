<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SpecifiedStaffUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
            'name' => 'Admin User',
            'email' => 'admin@rbcnigeria.com',
            'role' => 'admin',
            'password' => 'password',
            ],
            [
                'name' => 'Chika',
                'email' => 'Chika@rbcnigeria.com',
                'password' => 'Passwq5689',
                'role' => 'operations_manager', // read/write everything except user management
            ],
            [
                'name' => 'Grace',
                'email' => 'Grace@rbcnigeria.com',
                'password' => 'Passw3478',
                'role' => 'accountant', // ATC RW + Reports access
            ],
            [
                'name' => 'Victoria',
                'email' => 'Victoria@rbcnigeria.com',
                'password' => 'Past65457',
                'role' => 'operations_manager', // write everything except user mgmt and edit ATC
            ],
            [
                'name' => 'Nnamdi',
                'email' => 'nnamdi@rbcnigeria.com',
                'password' => 'yuor5632',
                'role' => 'movement_staff', // movement & maintenance and related reports
            ],
            [
                'name' => 'Rhoda',
                'email' => 'Rhoda@rbcnigeria.com',
                'password' => 'Timer5632',
                'role' => 'movement_staff', // movement & maintenance and related reports
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'], // hashed by cast
                    'role' => $data['role'],
                ]
            );
        }
    }
}
