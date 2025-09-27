<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class ImportDriversFromSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting import of drivers from SQL file...');
        
        // Read the SQL file
        $sqlFile = base_path('bbozvtsj_sales.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error('SQL file not found at: ' . $sqlFile);
            return;
        }
        
        $content = file_get_contents($sqlFile);
        
        // Extract drivers data using regex
        $pattern = '/INSERT INTO `all_drivers` \([^)]+\) VALUES\s*([^;]+);/s';
        preg_match($pattern, $content, $matches);
        
        if (empty($matches[1])) {
            $this->command->error('Could not extract drivers data from SQL file');
            return;
        }
        
        $driversData = $matches[1];
        
        // Parse the data
        $drivers = $this->parseDriversData($driversData);
        
        $this->command->info('Found ' . count($drivers) . ' drivers to import');
        
        // Import drivers
        $imported = 0;
        $skipped = 0;
        
        foreach ($drivers as $driverData) {
            try {
                // Clean and prepare data
                $name = trim($driverData['driver']);
                $phone = trim($driverData['phone']);
                $company = trim($driverData['company']);
                
                // Skip if name is empty
                if (empty($name)) {
                    $skipped++;
                    continue;
                }
                
                // Check if driver already exists
                $existingDriver = Driver::where('phone', $phone)->orWhere('name', $name)->first();
                
                if ($existingDriver) {
                    $skipped++;
                    continue;
                }
                
                // Create driver
                Driver::create([
                    'name' => $name,
                    'phone' => $phone,
                    'company' => $company ?: 'Unknown Company',
                    'status' => true,
                ]);
                
                $imported++;
                
                $this->command->info("Imported driver: {$name}");
                
            } catch (\Exception $e) {
                $this->command->error("Error importing driver {$name}: " . $e->getMessage());
                $skipped++;
            }
        }
        
        $this->command->info("Import completed. Imported: {$imported}, Skipped: {$skipped}");
    }
    
    /**
     * Parse drivers data from SQL INSERT statement
     */
    private function parseDriversData(string $data): array
    {
        $drivers = [];
        
        // Remove newlines and extra spaces
        $data = preg_replace('/\s+/', ' ', $data);
        
        // Split by ),( to get individual records
        $records = preg_split('/\),\s*\(/', $data);
        
        foreach ($records as $record) {
            // Clean up the record
            $record = trim($record, '()');
            
            // Split by comma, but be careful with quoted strings
            $fields = $this->splitRecordFields($record);
            
            if (count($fields) >= 6) {
                $drivers[] = [
                    'driver' => trim($fields[0], "'\" "),
                    'phone' => trim($fields[1], "'\" "),
                    'company' => trim($fields[2], "'\" "),
                    'did' => trim($fields[3], "'\" "),
                    'cab_no' => trim($fields[4], "'\" "),
                    'reg_no' => trim($fields[5], "'\" "),
                ];
            }
        }
        
        return $drivers;
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
