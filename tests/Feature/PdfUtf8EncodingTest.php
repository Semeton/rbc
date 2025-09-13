<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\ExportOutstandingBalancesPdf;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfUtf8EncodingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_export_handles_special_characters(): void
    {
        // Create customer with special characters in name
        $customer = Customer::factory()->create([
            'name' => 'Test Customer & Co. "Special" Characters',
        ]);
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 200.00,
        ]);

        $exportAction = new ExportOutstandingBalancesPdf;
        $response = $exportAction->execute();

        // Should not throw any UTF-8 encoding errors
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));

        // Verify PDF content is generated
        $pdfContent = $response->getContent();
        $this->assertNotEmpty($pdfContent);
        $this->assertGreaterThan(1000, strlen($pdfContent)); // PDF should be substantial
    }
}
