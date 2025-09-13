<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Reports\CustomerBalanceReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBalanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_balance_report_page_loads(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/reports/customer-balance');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.customer-balance');
    }

    public function test_customer_balance_report_service(): void
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
            'amount' => 1200.00,
            'payment_date' => now(),
        ]);

        $report = new CustomerBalanceReport;
        $data = $report->generate();
        $summary = $report->getSummary();
        $chartData = $report->getChartData();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_customers', $summary);
        $this->assertArrayHasKey('total_atc_value', $summary);
        $this->assertArrayHasKey('total_payments', $summary);
        $this->assertArrayHasKey('total_outstanding_balance', $summary);
        $this->assertArrayHasKey('customers_with_debt', $summary);
        $this->assertArrayHasKey('customers_with_credit', $summary);
    }

    public function test_customer_balance_report_with_filters(): void
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

        $report = new CustomerBalanceReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $this->assertNotEmpty($data);
        $this->assertEquals(1, $summary['total_customers']);
    }

    public function test_customer_balance_report_calculations(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        // Create transaction
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        // Create partial payment
        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 1200.00,
            'payment_date' => now(),
        ]);

        $report = new CustomerBalanceReport;
        $data = $report->generate();
        $summary = $report->getSummary();

        $customerData = $data->first();
        $this->assertNotNull($customerData);
        $this->assertEquals($customer->name, $customerData['customer_name']);
        $this->assertEquals(1500.0, $customerData['total_atc_value']); // 1000 + 500
        $this->assertEquals(1200.0, $customerData['total_payments']);
        $this->assertEquals(300.0, $customerData['outstanding_balance']); // 1500 - 1200
    }

    public function test_customer_balance_report_export(): void
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
            'amount' => 1200.00,
            'payment_date' => now(),
        ]);

        $report = new CustomerBalanceReport;
        $data = $report->generate();
        $summary = $report->getSummary();

        $this->assertNotEmpty($data);
        $this->assertNotEmpty($summary);
    }

    public function test_customer_balance_report_with_no_data(): void
    {
        $report = new CustomerBalanceReport;
        $data = $report->generate();
        $summary = $report->getSummary();

        $this->assertEmpty($data);
        $this->assertEquals(0, $summary['total_customers']);
        $this->assertEquals(0, $summary['total_atc_value']);
        $this->assertEquals(0, $summary['total_payments']);
        $this->assertEquals(0, $summary['total_outstanding_balance']);
    }

    public function test_customer_balance_report_summary_calculations(): void
    {
        // Create customers with different balance scenarios
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        $driver = Driver::factory()->create();

        // Customer 1: Has debt
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer1->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        // Customer 2: Has credit (overpaid)
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer2->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer2->id,
            'amount' => 2000.00,
            'payment_date' => now(),
        ]);

        $report = new CustomerBalanceReport;
        $summary = $report->getSummary();

        $this->assertEquals(2, $summary['total_customers']);
        $this->assertEquals(3000.0, $summary['total_atc_value']); // 1500 + 1500
        $this->assertEquals(2000.0, $summary['total_payments']);
        $this->assertEquals(1000.0, $summary['total_outstanding_balance']); // 3000 - 2000
        $this->assertEquals(1, $summary['customers_with_debt']);
        $this->assertEquals(1, $summary['customers_with_credit']);
    }
}
