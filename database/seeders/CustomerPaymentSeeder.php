<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Database\Seeder;

class CustomerPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customers
        $customers = Customer::all();
        
        // Create 3 payments for each customer
        foreach ($customers as $customer) {
            CustomerPayment::factory()
                ->count(3)
                ->create([
                    'customer_id' => $customer->id,
                ]);
        }
    }
}