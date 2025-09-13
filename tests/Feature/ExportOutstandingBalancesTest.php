<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\ExportOutstandingBalancesExcel;
use App\Actions\ExportOutstandingBalancesPdf;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportOutstandingBalancesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_export_generates_correct_response(): void
    {
        $customer = Customer::factory()->create();
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

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_excel_export_generates_correct_response(): void
    {
        $customer = Customer::factory()->create();
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

        $response = ExportOutstandingBalancesExcel::export();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    public function test_pdf_export_with_filters(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        $filters = [
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'customer_id' => $customer->id,
        ];

        $exportAction = new ExportOutstandingBalancesPdf;
        $response = $exportAction->execute($filters);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_excel_export_with_filters(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        $filters = [
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'customer_id' => $customer->id,
        ];

        $response = ExportOutstandingBalancesExcel::export($filters);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    public function test_livewire_export_methods(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        $this->actingAs($user)
            ->get('/reports/outstanding-balances')
            ->assertStatus(200)
            ->assertSeeLivewire('reports.outstanding-balances');
    }
}
