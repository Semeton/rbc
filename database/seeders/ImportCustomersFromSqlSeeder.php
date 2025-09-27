<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportCustomersFromSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting import of customers from SQL file...');
        
        // Read the SQL file
        $sqlFile = base_path('bbozvtsj_sales.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error('SQL file not found at: ' . $sqlFile);
            return;
        }
        
        $content = file_get_contents($sqlFile);
        
        // Extract customers data using regex
        $pattern = '/INSERT INTO `customers` \([^)]+\) VALUES\s*([^;]+);/s';
        preg_match($pattern, $content, $matches);
        
        if (empty($matches[1])) {
            $this->command->error('Could not extract customers data from SQL file');
            return;
        }
        
        $customersData = $matches[1];
        
        // Parse the data
        $customers = $this->parseCustomersData($customersData);
        
        $this->command->info('Found ' . count($customers) . ' customers to import');
        
        // Import customers
        $imported = 0;
        $skipped = 0;
        
        foreach ($customers as $customerData) {
            try {
                // Skip customers marked as HIDE
                if (isset($customerData['hos']) && $customerData['hos'] === 'HIDE') {
                    $skipped++;
                    continue;
                }
                
                // Clean and prepare data
                $name = trim($customerData['name_of_client']);
                $phone = trim($customerData['phone_of_client']);
                $email = trim($customerData['email_of_client']);
                $notes = trim($customerData['note']);
                
                // Skip if name is empty
                if (empty($name)) {
                    $skipped++;
                    continue;
                }
                
                // Generate email if not provided
                if (empty($email)) {
                    $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
                }
                
                // Check if customer already exists
                $existingCustomer = Customer::where('phone', $phone)->orWhere('email', $email)->first();
                
                if ($existingCustomer) {
                    $skipped++;
                    continue;
                }
                
                // Create customer
                Customer::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'notes' => $notes,
                    'status' => true,
                ]);
                
                $imported++;
                
                if ($imported % 100 === 0) {
                    $this->command->info("Imported {$imported} customers...");
                }
                
            } catch (\Exception $e) {
                $this->command->error("Error importing customer {$name}: " . $e->getMessage());
                $skipped++;
            }
        }
        
        $this->command->info("Import completed. Imported: {$imported}, Skipped: {$skipped}");
    }
    
    /**
     * Parse customers data from SQL INSERT statement
     */
    private function parseCustomersData(string $data): array
    {
        $customers = [];
        
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
                $customers[] = [
                    'id' => trim($fields[0], "'\" "),
                    'phone_of_client' => trim($fields[1], "'\" "),
                    'note' => trim($fields[2], "'\" "),
                    'name_of_client' => trim($fields[3], "'\" "),
                    'email_of_client' => trim($fields[4], "'\" "),
                    'hos' => trim($fields[5], "'\" "),
                ];
            }
        }
        
        return $customers;
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
