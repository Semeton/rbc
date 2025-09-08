<?php

namespace Database\Seeders;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        // Create 20 audit trail records
        AuditTrail::factory()
            ->count(20)
            ->create([
                'user_id' => function () use ($users) {
                    return $users->random()->id;
                },
            ]);
    }
}