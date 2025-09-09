<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use App\Models\User;
use App\Reports\CustomerBalanceReport;
use App\Reports\DriverPerformanceReport;
use App\Reports\MaintenanceCostReport;
use App\Reports\MonthlySalesReport;
use App\Reports\TruckUtilizationReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
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

    public function test_reports_index_page_loads(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_customer_balance_report_endpoint(): void
    {
        $customer = Customer::factory()->create();

        // Create some transactions and payments
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'atc_cost' => 600.00,
            'transport_cost' => 400.00,
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 500.00,
        ]);

        $response = $this->get(route('reports.customer-balance'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'summary',
            'filters',
        ]);
    }

    public function test_monthly_sales_report_endpoint(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 900.00,
            'transport_cost' => 600.00,
        ]);

        $response = $this->get(route('reports.monthly-sales'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'summary',
            'filters',
        ]);
    }

    public function test_driver_performance_report_endpoint(): void
    {
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'driver_id' => $driver->id,
            'atc_cost' => 1200.00,
            'transport_cost' => 800.00,
        ]);

        $response = $this->get(route('reports.driver-performance'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'summary',
            'filters',
        ]);
    }

    public function test_truck_utilization_report_endpoint(): void
    {
        $truck = Truck::factory()->create();

        DailyTruckRecord::factory()->create([
            'truck_id' => $truck->id,
            'fare' => 2500.00,
        ]);

        $response = $this->get(route('reports.truck-utilization'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'summary',
            'filters',
        ]);
    }

    public function test_maintenance_cost_report_endpoint(): void
    {
        $truck = Truck::factory()->create();

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 1000.00,
        ]);

        $response = $this->get(route('reports.maintenance-cost'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'summary',
            'filters',
        ]);
    }

    public function test_export_report_endpoint(): void
    {
        $response = $this->get(route('reports.export', 'customer-balance'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'report_type',
            'format',
            'filters',
        ]);
    }

    public function test_customer_balance_report_service(): void
    {
        $customer = Customer::factory()->create();

        // Create transactions and payments
        DailyCustomerTransaction::factory()->count(3)->create([
            'customer_id' => $customer->id,
            'atc_cost' => 600.00,
            'transport_cost' => 400.00,
        ]);

        CustomerPayment::factory()->count(2)->create([
            'customer_id' => $customer->id,
            'amount' => 500.00,
        ]);

        $report = new CustomerBalanceReport;
        $data = $report->generate();
        $summary = $report->getSummary();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_customers', $summary);
        $this->assertArrayHasKey('total_closing_balance', $summary);
    }

    public function test_monthly_sales_report_service(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->count(5)->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 900.00,
            'transport_cost' => 600.00,
        ]);

        $report = new MonthlySalesReport;
        $data = $report->generate();
        $summary = $report->getSummary();
        $topCustomers = $report->getTopCustomers();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_revenue', $summary);
        $this->assertArrayHasKey('net_profit', $summary);
        $this->assertNotEmpty($topCustomers);
    }

    public function test_driver_performance_report_service(): void
    {
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->count(4)->create([
            'driver_id' => $driver->id,
            'atc_cost' => 1200.00,
            'transport_cost' => 800.00,
        ]);

        $report = new DriverPerformanceReport;
        $data = $report->generate();
        $summary = $report->getSummary();
        $topPerformers = $report->getTopPerformers();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_revenue', $summary);
        $this->assertArrayHasKey('average_revenue_per_driver', $summary);
        $this->assertNotEmpty($topPerformers);
    }

    public function test_truck_utilization_report_service(): void
    {
        $truck = Truck::factory()->create();

        DailyTruckRecord::factory()->count(3)->create([
            'truck_id' => $truck->id,
            'fare' => 2500.00,
        ]);

        $report = new TruckUtilizationReport;
        $data = $report->generate();
        $summary = $report->getSummary();
        $topUtilized = $report->getTopUtilized();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_trucks', $summary);
        $this->assertArrayHasKey('average_utilization', $summary);
        $this->assertNotEmpty($topUtilized);
    }

    public function test_maintenance_cost_report_service(): void
    {
        $truck = Truck::factory()->create();

        TruckMaintenanceRecord::factory()->count(2)->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 1000.00,
        ]);

        $report = new MaintenanceCostReport;
        $data = $report->generate();
        $summary = $report->getSummary();
        $monthlyTrend = $report->getMonthlyTrend();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_cost', $summary);
        $this->assertArrayHasKey('average_cost_per_maintenance', $summary);
        $this->assertNotEmpty($monthlyTrend);
    }

    public function test_reports_with_filters(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 900.00,
            'transport_cost' => 600.00,
        ]);

        $filters = [
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->endOfMonth()->format('Y-m-d'),
        ];

        $report = new MonthlySalesReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('total_revenue', $summary);
    }
}
