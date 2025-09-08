<?php

namespace Database\Seeders;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use Illuminate\Database\Seeder;

class DailyCustomerTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customers, drivers, and ATCs
        $customers = Customer::all();
        $drivers = Driver::all();
        $atcs = Atc::all();
        
        // Create 5 daily transactions for each customer
        foreach ($customers as $customer) {
            $customerAtcs = $atcs->where('customer_id', $customer->id);
            
            // Skip if customer has no ATCs
            if ($customerAtcs->isEmpty()) {
                continue;
            }
            
            for ($i = 0; $i < 5; $i++) {
                DailyCustomerTransaction::factory()->create([
                    'customer_id' => $customer->id,
                    'driver_id' => $drivers->random()->id,
                    'atc_id' => $customerAtcs->random()->id,
                ]);
            }
        }
    }
}