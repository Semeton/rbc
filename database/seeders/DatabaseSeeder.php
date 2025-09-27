<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in the correct order based on dependencies
        $this->call([
            UserSeeder::class,           // Users first for foreign key relationships
            ImportCustomersFromSqlSeeder::class,       // Customers before their related records
            ImportDriversFromSqlSeeder::class,         // Drivers before daily records
            ImportTrucksFromSqlSeeder::class,
            ImportDriversFromSqlSeeder::class,
            ImportDtrFromSqlSeeder::class,
            // TruckSeeder::class,          // Trucks before maintenance records
            // AtcSeeder::class,            // ATCs before daily transactions
            // CustomerPaymentSeeder::class,
            // DailyCustomerTransactionSeeder::class,
            // TruckMaintenanceRecordSeeder::class,
            // AuditTrailSeeder::class,
            // ActivityLogSeeder::class,
        ]);
    }
}
