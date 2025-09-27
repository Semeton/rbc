<?php

namespace Database\Seeders;

use App\Models\Truck;
use Illuminate\Database\Seeder;

class ImportTrucksFromSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting import of trucks from SQL file...');
        
        // Read the SQL file
        $sqlFile = base_path('bbozvtsj_sales.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error('SQL file not found at: ' . $sqlFile);
            return;
        }
        
        $content = file_get_contents($sqlFile);
        
        // Extract trucks data using regex
        $pattern = '/INSERT INTO `trucks` \([^)]+\) VALUES\s*([^;]+);/s';
        preg_match_all($pattern, $content, $matches);
        
        if (empty($matches[1])) {
            $this->command->error('Could not extract trucks data from SQL file');
            return;
        }
        
        // Combine all truck data from multiple INSERT statements
        $allTrucksData = implode(', ', $matches[1]);
        
        // Parse the data
        $trucks = $this->parseTrucksData($allTrucksData);
        
        $this->command->info('Found ' . count($trucks) . ' trucks to import');
        
        // Import trucks
        $imported = 0;
        $skipped = 0;
        
        foreach ($trucks as $truckData) {
            try {
                // Clean and prepare data
                $cabNo = trim($truckData['cab_no']);
                $regNo = trim($truckData['reg_no']);
                
                // Skip if essential data is missing
                if (empty($cabNo) || empty($regNo)) {
                    $skipped++;
                    continue;
                }
                
                // Check if truck already exists (by registration number or cab number)
                $existingTruck = Truck::where('registration_number', $regNo)
                    ->orWhere('cab_number', $cabNo)
                    ->first();
                
                if ($existingTruck) {
                    $skipped++;
                    continue;
                }
                
                // Determine truck model from registration number pattern
                $truckModel = $this->determineTruckModel($regNo);
                
                // Determine year of manufacture (default to 2020 if not determinable)
                $yearOfManufacture = $this->determineYearOfManufacture($regNo);
                
                // Create truck
                Truck::create([
                    'cab_number' => $cabNo,
                    'registration_number' => $regNo,
                    'truck_model' => $truckModel,
                    'year_of_manufacture' => $yearOfManufacture,
                    'status' => true,
                ]);
                
                $imported++;
                
                if ($imported % 50 === 0) {
                    $this->command->info("Imported {$imported} trucks...");
                }
                
            } catch (\Exception $e) {
                $this->command->error("Error importing truck {$cabNo}: " . $e->getMessage());
                $skipped++;
            }
        }
        
        $this->command->info("Import completed. Imported: {$imported}, Skipped: {$skipped}");
    }
    
    /**
     * Determine truck model from registration number
     */
    private function determineTruckModel(string $regNo): string
    {
        $modelMap = [
            'WDL ' => 'Howo Truck',
            'RMG ' => 'Sino Truck', 
            'SNN ' => 'JAC Truck',
            'UGG ' => 'Foton Truck',
            'GWL ' => 'FAW Truck',
            'GRZ ' => 'Isuzu Truck',
            'XB ' => 'MAN Truck',
            'ABJ ' => 'Mercedes Truck',
            'TRN ' => 'Tata Truck',
            'ERE ' => 'Dongfeng Truck',
            'T-' => 'Trailer Truck',
            'RBC ' => 'Company Truck',
        ];
        
        // Check for exact matches first
        foreach ($modelMap as $prefix => $model) {
            if (strpos($regNo, $prefix) === 0) {
                return $model;
            }
        }
        
        // Check for partial matches (case insensitive)
        $regNoUpper = strtoupper($regNo);
        foreach ($modelMap as $prefix => $model) {
            if (strpos($regNoUpper, strtoupper($prefix)) === 0) {
                return $model;
            }
        }
        
        // Check if it contains any of the prefixes
        foreach ($modelMap as $prefix => $model) {
            if (strpos($regNoUpper, strtoupper($prefix)) !== false) {
                return $model;
            }
        }
        
        return 'Unknown Model';
    }
    
    /**
     * Determine year of manufacture from registration number
     */
    private function determineYearOfManufacture(string $regNo): int
    {
        // Look for year patterns in registration number
        if (preg_match('/20(\d{2})/', $regNo, $matches)) {
            $year = 2000 + (int)$matches[1];
            if ($year >= 2010 && $year <= 2024) {
                return $year;
            }
        }
        
        // Default to 2020 for unknown years
        return 2020;
    }
    
    /**
     * Parse trucks data from SQL INSERT statement
     */
    private function parseTrucksData(string $data): array
    {
        $trucks = [];
        
        // Remove newlines and extra spaces
        $data = preg_replace('/\s+/', ' ', $data);
        
        // Split by ),( to get individual records
        $recordStrings = preg_split('/\),\s*\(/', $data);
        
        foreach ($recordStrings as $recordString) {
            // Clean up the record
            $recordString = trim($recordString, '()');
            
            // Split by comma, but be careful with quoted strings
            $fields = $this->splitRecordFields($recordString);
            
            if (count($fields) >= 3) {
                $trucks[] = [
                    'tid' => trim($fields[0], "'\" "),
                    'cab_no' => trim($fields[1], "'\" "),
                    'reg_no' => trim($fields[2], "'\" "),
                ];
            }
        }
        
        return $trucks;
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
