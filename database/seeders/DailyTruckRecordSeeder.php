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
        $this->call(ImportDtrFromSqlSeeder::class);
    }
}