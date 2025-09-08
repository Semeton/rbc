<?php

namespace Database\Seeders;

use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use Illuminate\Database\Seeder;

class TruckMaintenanceRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all trucks
        $trucks = Truck::all();
        
        // Create 2 maintenance records for each truck
        foreach ($trucks as $truck) {
            TruckMaintenanceRecord::factory()
                ->count(2)
                ->create([
                    'truck_id' => $truck->id,
                ]);
        }
    }
}