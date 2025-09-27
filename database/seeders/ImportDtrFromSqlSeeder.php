<?php

namespace Database\Seeders;

use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class ImportDtrFromSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting import of DTR (Daily Truck Records) from SQL file...');
        
        // Read the SQL file
        $sqlFile = base_path('bbozvtsj_sales.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error('SQL file not found at: ' . $sqlFile);
            return;
        }
        
        $content = file_get_contents($sqlFile);
        
        // Extract DTR data using regex
        $pattern = '/INSERT INTO `dtr` \([^)]+\) VALUES\s*([^;]+);/s';
        preg_match($pattern, $content, $matches);
        
        if (empty($matches[1])) {
            $this->command->error('Could not extract DTR data from SQL file');
            return;
        }
        
        $dtrData = $matches[1];
        
        // Parse the data
        $dtrRecords = $this->parseDtrData($dtrData);
        
        $this->command->info('Found ' . count($dtrRecords) . ' DTR records to import');
        
        // Import DTR records
        $imported = 0;
        $skipped = 0;
        $createdEntities = [
            'drivers' => 0,
            'trucks' => 0,
            'customers' => 0
        ];
        
        foreach ($dtrRecords as $dtrData) {
            try {
                // Clean and prepare data
                $driverName = trim($dtrData['driver']);
                $cabNo = trim($dtrData['cab_no']);
                $customerName = trim($dtrData['rec_cust']);
                $destination = trim($dtrData['destination']);
                
                // Skip if essential data is missing
                if (empty($driverName) || empty($cabNo)) {
                    $skipped++;
                    continue;
                }
                
                // Find or create driver
                $driver = $this->findOrCreateDriver($driverName);
                if ($driver->wasRecentlyCreated) {
                    $createdEntities['drivers']++;
                }
                
                // Find or create truck
                $truck = $this->findOrCreateTruck($cabNo);
                if ($truck->wasRecentlyCreated) {
                    $createdEntities['trucks']++;
                }
                
                // Find or create customer
                $customer = $this->findOrCreateCustomer($customerName);
                if ($customer->wasRecentlyCreated) {
                    $createdEntities['customers']++;
                }
                
                // Parse dates
                $atcCollectionDate = $this->parseDate($dtrData['atc_col_date']);
                $dispatchDate = $this->parseDate($dtrData['dispatch_date']);
                
                // Parse amounts (remove commas and convert to float)
                $fare = $this->parseAmount($dtrData['fare']);
                $gasChopMoney = $this->parseAmount($dtrData['g_c_money']);
                $balance = $this->parseAmount($dtrData['balance']);
                
                // Check if record already exists
                $existingRecord = DailyTruckRecord::where('driver_id', $driver->id)
                    ->where('truck_id', $truck->id)
                    ->where('customer_id', $customer->id)
                    ->where('atc_collection_date', $atcCollectionDate)
                    ->where('load_dispatch_date', $dispatchDate)
                    ->first();
                
                if ($existingRecord) {
                    $skipped++;
                    continue;
                }
                
                // Create DTR record
                DailyTruckRecord::create([
                    'driver_id' => $driver->id,
                    'truck_id' => $truck->id,
                    'customer_id' => $customer->id,
                    'atc_collection_date' => $atcCollectionDate,
                    'load_dispatch_date' => $dispatchDate,
                    'destination' => $destination,
                    'fare' => $fare,
                    'gas_chop_money' => $gasChopMoney,
                    'balance' => $balance,
                    'status' => true,
                ]);
                
                $imported++;
                
                if ($imported % 50 === 0) {
                    $this->command->info("Imported {$imported} DTR records...");
                }
                
            } catch (\Exception $e) {
                $this->command->error("Error importing DTR record for driver {$driverName}: " . $e->getMessage());
                $skipped++;
            }
        }
        
        $this->command->info("Import completed.");
        $this->command->info("DTR Records - Imported: {$imported}, Skipped: {$skipped}");
        $this->command->info("New entities created - Drivers: {$createdEntities['drivers']}, Trucks: {$createdEntities['trucks']}, Customers: {$createdEntities['customers']}");
    }
    
    /**
     * Find or create driver
     */
    private function findOrCreateDriver(string $driverName): Driver
    {
        $driver = Driver::where('name', $driverName)->first();
        
        if (!$driver) {
            $driver = Driver::create([
                'name' => $driverName,
                'phone' => '0000000000', // Default phone
                'company' => 'Unknown Company',
                'status' => true,
            ]);
        }
        
        return $driver;
    }
    
    /**
     * Find or create truck
     */
    private function findOrCreateTruck(string $cabNo): Truck
    {
        $truck = Truck::where('cab_number', $cabNo)->first();
        
        if (!$truck) {
            $truck = Truck::create([
                'cab_number' => $cabNo,
                'registration_number' => $cabNo, // Use cab_no as registration_number if not available
                'truck_model' => 'Unknown Model',
                'year_of_manufacture' => 2020, // Default year
                'status' => true,
            ]);
        }
        
        return $truck;
    }
    
    /**
     * Find or create customer
     */
    private function findOrCreateCustomer(string $customerName): Customer
    {
        $customer = Customer::where('name', $customerName)->first();
        
        if (!$customer) {
            $customer = Customer::create([
                'name' => $customerName,
                'phone' => '0000000000', // Default phone
                'email' => strtolower(str_replace(' ', '.', $customerName)) . '@example.com',
                'notes' => 'Created from DTR import',
                'status' => true,
            ]);
        }
        
        return $customer;
    }
    
    /**
     * Parse date string to Carbon instance
     */
    private function parseDate(string $dateString): ?\Carbon\Carbon
    {
        if (empty($dateString) || $dateString === '0000-00-00') {
            return null;
        }
        
        try {
            // Fix common date format issues
            $fixedDateString = $this->fixMalformedDate($dateString);
            
            return \Carbon\Carbon::parse($fixedDateString);
        } catch (\Exception $e) {
            $this->command->warn("Could not parse date: {$dateString} - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Fix malformed dates (e.g., 2924-06-06 -> 2024-06-06)
     */
    private function fixMalformedDate(string $dateString): string
    {
        // Pattern to match YYYY-MM-DD format
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateString, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];
            
            // Fix common year typos
            if ($year === '2924') {
                $year = '2024';
            } elseif ($year === '0004') {
                $year = '2024';
            } elseif ($year === '1024') {
                $year = '2024';
            } elseif ($year === '0024') {
                $year = '2024';
            }
            
            // Validate year range
            if ($year < 1900 || $year > 2100) {
                $year = '2024'; // Default to 2024 if year is unreasonable
            }
            
            return "{$year}-{$month}-{$day}";
        }
        
        return $dateString;
    }
    
    /**
     * Parse amount string to float
     */
    private function parseAmount(string $amountString): ?float
    {
        if (empty($amountString) || $amountString === '' || $amountString === '0') {
            return 0.0;
        }
        
        // Remove commas and any non-numeric characters except decimal point and minus sign
        $cleaned = preg_replace('/[^0-9.-]/', '', $amountString);
        
        if (empty($cleaned)) {
            return 0.0;
        }
        
        return (float) $cleaned;
    }
    
    /**
     * Parse DTR data from SQL INSERT statement
     */
    private function parseDtrData(string $data): array
    {
        $records = [];
        
        // Remove newlines and extra spaces
        $data = preg_replace('/\s+/', ' ', $data);
        
        // Split by ),( to get individual records
        $recordStrings = preg_split('/\),\s*\(/', $data);
        
        foreach ($recordStrings as $recordString) {
            // Clean up the record
            $recordString = trim($recordString, '()');
            
            // Split by comma, but be careful with quoted strings
            $fields = $this->splitRecordFields($recordString);
            
            if (count($fields) >= 10) {
                $records[] = [
                    'id' => trim($fields[0], "'\" "),
                    'driver' => trim($fields[1], "'\" "),
                    'cab_no' => trim($fields[2], "'\" "),
                    'atc_col_date' => trim($fields[3], "'\" "),
                    'dispatch_date' => trim($fields[4], "'\" "),
                    'rec_cust' => trim($fields[5], "'\" "),
                    'destination' => trim($fields[6], "'\" "),
                    'fare' => trim($fields[7], "'\" "),
                    'g_c_money' => trim($fields[8], "'\" "),
                    'balance' => trim($fields[9], "'\" "),
                ];
            }
        }
        
        return $records;
    }
    
    /**
     * Split record fields considering quoted strings
     */
    private function splitRecordFields(string $record): array
    {
        $fields = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = null;
        
        for ($i = 0; $i < strlen($record); $i++) {
            $char = $record[$i];
            
            if (!$inQuotes && ($char === "'" || $char === '"')) {
                $inQuotes = true;
                $quoteChar = $char;
                $current .= $char;
            } elseif ($inQuotes && $char === $quoteChar) {
                $inQuotes = false;
                $quoteChar = null;
                $current .= $char;
            } elseif (!$inQuotes && $char === ',') {
                $fields[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        if (!empty($current)) {
            $fields[] = $current;
        }
        
        return $fields;
    }
}
