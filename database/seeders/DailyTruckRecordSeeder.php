<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Database\Seeder;

class DailyTruckRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trucks = Truck::all();
        $drivers = Driver::all();
        $customers = Customer::all();

        foreach ($trucks as $truck) {
            DailyTruckRecord::factory()
                ->count(3)
                ->create([
                    'truck_id' => $truck->id,
                    'driver_id' => $drivers->random()->id,
                    'customer_id' => $customers->random()->id,
                ]);
        }
    }
}