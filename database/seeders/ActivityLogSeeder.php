<?php

namespace Database\Seeders;

use App\Models\ActtivityLog;
use App\Models\Customer;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users, customers, and trucks
        $users = User::all();
        $customers = Customer::all();
        $trucks = Truck::all();
        
        // Create 10 activity logs for customers
        foreach ($customers->random(min(10, $customers->count())) as $customer) {
            ActtivityLog::factory()->create([
                'user_id' => $users->random()->id,
                'loggable_type' => Customer::class,
                'loggable_id' => $customer->id,
            ]);
        }
        
        // Create 10 activity logs for trucks
        foreach ($trucks->random(min(10, $trucks->count())) as $truck) {
            ActtivityLog::factory()->create([
                'user_id' => $users->random()->id,
                'loggable_type' => Truck::class,
                'loggable_id' => $truck->id,
            ]);
        }
    }
}