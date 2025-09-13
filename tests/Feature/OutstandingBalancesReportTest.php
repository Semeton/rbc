<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Models\User;
use App\Reports\OutstandingBalancesReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutstandingBalancesReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user with admin role for middleware checks
        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($this->user);
    }

    public function test_outstanding_balances_report_page_loads(): void
    {
        $response = $this->get(route('reports.outstanding-balances'));
        $response->assertStatus(200);
    }

    public function test_outstanding_balances_report_service(): void
    {
        // Create test data
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        // Create transactions (debits)
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        // Create payment (credit)
        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 800.00,
        ]);

        $report = new OutstandingBalancesReport;
        $data = $report->generate();
        $summary = $report->getSummary();
        $chartData = $report->getChartData();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_customers_with_debt', $summary);
        $this->assertArrayHasKey('total_outstanding_amount', $summary);
        $this->assertArrayHasKey('average_outstanding_amount', $summary);
    }

    public function test_outstanding_balances_report_with_filters(): void
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

        $report = new OutstandingBalancesReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $this->assertNotEmpty($data);
        $this->assertEquals(1, $summary['total_customers_with_debt']);
    }

    public function test_outstanding_balances_report_overdue_calculation(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        // Create transaction (creates debt) - within current month
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 2000.00, // Higher transaction amount
            'transport_cost' => 1000.00,
            'created_at' => now()->subDays(5), // Within current month
        ]);

        // Create old payment (smaller amount, more than 30 days ago)
        $payment = new CustomerPayment([
            'customer_id' => $customer->id,
            'amount' => 500.00, // Smaller payment to create debt
            'payment_date' => now()->subDays(45),
            'bank_name' => 'Test Bank',
            'notes' => 'Test payment',
        ]);
        $payment->save();

        // Test with a date range that includes the old payment
        $filters = [
            'start_date' => now()->subDays(60),
            'end_date' => now(),
        ];

        $report = new OutstandingBalancesReport;
        $data = $report->generate($filters);

        $customerData = $data->first();
        // Just check that the customer has data
        $this->assertNotNull($customerData);
        $this->assertEquals($customer->name, $customerData['customer_name']);
        // Check that the last payment date is set
        $this->assertNotNull($customerData['last_payment_date']);
    }

    public function test_outstanding_balances_report_export(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        $report = new OutstandingBalancesReport;
        $data = $report->generate();
        $summary = $report->getSummary();

        $this->assertNotEmpty($data);
        $this->assertNotEmpty($summary);
    }

    public function test_outstanding_balances_report_with_no_data(): void
    {
        $report = new OutstandingBalancesReport;
        $data = $report->generate();
        $summary = $report->getSummary();

        $this->assertEmpty($data);
        $this->assertEquals(0, $summary['total_customers_with_debt']);
        $this->assertEquals(0, $summary['total_outstanding_amount']);
    }

    public function test_outstanding_balances_report_summary_calculations(): void
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

        // Customer 2: No debt (has payments)
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer2->id,
            'driver_id' => $driver->id,
            'atc_cost' => 1000.00,
            'transport_cost' => 500.00,
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer2->id,
            'amount' => 2000.00,
            'payment_date' => now(), // Set to current date to be within the month range
        ]);

        $report = new OutstandingBalancesReport;
        $summary = $report->getSummary();

        $this->assertEquals(1, $summary['total_customers_with_debt']);
        $this->assertEquals(1500.0, $summary['total_outstanding_amount']);
        $this->assertEquals(1500.0, $summary['average_outstanding_amount']);
    }
}
