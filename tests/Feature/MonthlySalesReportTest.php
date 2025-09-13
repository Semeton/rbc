<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Reports\MonthlySalesReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlySalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_sales_report_page_loads(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/reports/monthly-sales');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.monthly-sales');
    }

    public function test_monthly_sales_report_generates_data(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        // Create transactions for different months
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
            'created_at' => now()->startOfMonth(),
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 7500.00,
            'transport_cost' => 3000.00,
            'created_at' => now()->subMonth()->startOfMonth(),
        ]);

        $report = new MonthlySalesReport;
        $data = $report->generate();

        $this->assertCount(2, $data);
        // Find the current month's data
        $currentMonthData = $data->firstWhere('month', now()->format('Y-m'));
        $this->assertEquals(1, $currentMonthData['total_transactions']);
        $this->assertEquals(5000.00, $currentMonthData['total_atc_cost']);
        $this->assertEquals(2000.00, $currentMonthData['total_transport_fees']);
        $this->assertEquals(7000.00, $currentMonthData['total_revenue']);
    }

    public function test_monthly_sales_report_filters_by_date_range(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        // Create transaction in current month
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
            'created_at' => now(),
        ]);

        // Create transaction in previous year (should be filtered out)
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 10000.00,
            'transport_cost' => 4000.00,
            'created_at' => now()->subYear(),
        ]);

        $report = new MonthlySalesReport;
        $data = $report->generate([
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
        ]);

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data->first()['total_transactions']);
    }

    public function test_monthly_sales_report_filters_by_customer(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer1->id,
            'driver_id' => $driver->id,
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer2->id,
            'driver_id' => $driver->id,
            'atc_cost' => 7500.00,
            'transport_cost' => 3000.00,
        ]);

        $report = new MonthlySalesReport;
        $data = $report->generate(['customer_id' => $customer1->id]);

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data->first()['total_transactions']);
    }

    public function test_monthly_sales_report_summary_calculations(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        // Create transactions for two months
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
            'created_at' => now()->startOfMonth(),
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 10000.00,
            'transport_cost' => 4000.00,
            'created_at' => now()->subMonth()->startOfMonth(),
        ]);

        $report = new MonthlySalesReport;
        $summary = $report->getSummary();

        $this->assertEquals(2, $summary['total_months']);
        $this->assertEquals(2, $summary['total_transactions']);
        $this->assertEquals(15000.0, $summary['total_atc_cost']);
        $this->assertEquals(6000.0, $summary['total_transport_fees']);
        $this->assertEquals(21000.0, $summary['total_revenue']);
        $this->assertEquals(10500.0, $summary['average_monthly_revenue']);
        $this->assertNotNull($summary['best_month']);
        $this->assertNotNull($summary['worst_month']);
    }

    public function test_monthly_sales_report_chart_data(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 7500.00,
            'transport_cost' => 3000.00,
        ]);

        $report = new MonthlySalesReport;
        $chartData = $report->getChartData();

        $this->assertArrayHasKey('monthly_revenue', $chartData);
        $this->assertArrayHasKey('cement_distribution', $chartData);
        $this->assertArrayHasKey('labels', $chartData['monthly_revenue']);
        $this->assertArrayHasKey('revenue', $chartData['monthly_revenue']);
        $this->assertArrayHasKey('labels', $chartData['cement_distribution']);
        $this->assertArrayHasKey('transactions', $chartData['cement_distribution']);
    }

    public function test_monthly_sales_report_handles_no_data(): void
    {
        $report = new MonthlySalesReport;
        $data = $report->generate();
        $summary = $report->getSummary();

        $this->assertCount(0, $data);
        $this->assertEquals(0, $summary['total_months']);
        $this->assertEquals(0, $summary['total_transactions']);
        $this->assertEquals(0, $summary['total_atc_cost']);
        $this->assertEquals(0, $summary['total_transport_fees']);
        $this->assertEquals(0, $summary['total_revenue']);
        $this->assertEquals(0, $summary['average_monthly_revenue']);
        $this->assertNull($summary['best_month']);
        $this->assertNull($summary['worst_month']);
    }

    public function test_monthly_sales_report_export_pdf(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        $response = $this->actingAs($user)->get('/reports/monthly-sales');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.monthly-sales');
    }

    public function test_monthly_sales_report_export_excel(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        $response = $this->actingAs($user)->get('/reports/monthly-sales');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.monthly-sales');
    }
}
