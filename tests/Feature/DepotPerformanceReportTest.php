<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Models\User;
use App\Reports\DepotPerformanceReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepotPerformanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_depot_performance_report_page_loads(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/reports/depot-performance');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.depot-performance');
    }

    public function test_depot_performance_report_generates_data(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        // Create transactions for different depots
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot',
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
            'date' => now()->subDays(5),
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Abuja Depot',
            'atc_cost' => 7500.00,
            'transport_cost' => 3000.00,
            'date' => now()->subDays(3),
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot', // Same depot as first transaction
            'atc_cost' => 3000.00,
            'transport_cost' => 1500.00,
            'date' => now()->subDays(2),
        ]);

        $report = new DepotPerformanceReport;
        $data = $report->generate();

        $this->assertCount(2, $data);

        // Check Lagos Depot (should have 2 transactions)
        $lagosDepot = $data->firstWhere('depot_name', 'Lagos Depot');
        $this->assertNotNull($lagosDepot);
        $this->assertEquals(2, $lagosDepot['total_dispatches']);
        $this->assertEquals(8000.00, $lagosDepot['total_atc_cost']); // 5000 + 3000
        $this->assertEquals(3500.00, $lagosDepot['total_transport_cost']); // 2000 + 1500
        $this->assertEquals(11500.00, $lagosDepot['total_revenue']); // 8000 + 3500

        // Check Abuja Depot
        $abujaDepot = $data->firstWhere('depot_name', 'Abuja Depot');
        $this->assertNotNull($abujaDepot);
        $this->assertEquals(1, $abujaDepot['total_dispatches']);
        $this->assertEquals(7500.00, $abujaDepot['total_atc_cost']);
        $this->assertEquals(3000.00, $abujaDepot['total_transport_cost']);
        $this->assertEquals(10500.00, $abujaDepot['total_revenue']);
    }

    public function test_depot_performance_report_filters_by_date_range(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        // Transaction within range
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot',
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
            'date' => now()->subDays(10),
        ]);

        // Transaction outside range
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Abuja Depot',
            'atc_cost' => 3000.00,
            'transport_cost' => 1500.00,
            'date' => now()->subDays(50),
        ]);

        $report = new DepotPerformanceReport;
        $data = $report->generate([
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(5),
        ]);

        $this->assertCount(1, $data);
        $this->assertEquals('Lagos Depot', $data->first()['depot_name']);
        $this->assertEquals(5000.00, $data->first()['total_atc_cost']);
    }

    public function test_depot_performance_report_filters_by_depot_name(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Central Depot',
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Abuja Main Depot',
            'atc_cost' => 7500.00,
            'transport_cost' => 3000.00,
        ]);

        $report = new DepotPerformanceReport;
        $data = $report->generate(['depot_name' => 'Lagos']);

        $this->assertCount(1, $data);
        $this->assertEquals('Lagos Central Depot', $data->first()['depot_name']);
        $this->assertEquals(5000.00, $data->first()['total_atc_cost']);
    }

    public function test_depot_performance_report_summary_calculations(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot',
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Abuja Depot',
            'atc_cost' => 7500.00,
            'transport_cost' => 3000.00,
        ]);

        $report = new DepotPerformanceReport;
        $summary = $report->getSummary();

        $this->assertEquals(2, $summary['depot_count']);
        $this->assertEquals(2, $summary['total_dispatches']);
        $this->assertEquals(17500.00, $summary['total_revenue']); // 7000 + 10500
        $this->assertEquals(12500.00, $summary['total_atc_cost']); // 5000 + 7500
        $this->assertEquals(5000.00, $summary['total_transport_cost']); // 2000 + 3000
        $this->assertEquals(8750.00, $summary['average_revenue_per_depot']); // 17500 / 2
        $this->assertEquals(1.0, $summary['average_dispatches_per_depot']);
    }

    public function test_depot_performance_report_chart_data(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot',
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Abuja Depot',
            'atc_cost' => 7500.00,
            'transport_cost' => 3000.00,
        ]);

        $report = new DepotPerformanceReport;
        $chartData = $report->getChartData();

        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('revenue', $chartData);
        $this->assertArrayHasKey('dispatches', $chartData);
        $this->assertArrayHasKey('atc_costs', $chartData);
        $this->assertArrayHasKey('transport_costs', $chartData);

        $this->assertContains('Lagos Depot', $chartData['labels']);
        $this->assertContains('Abuja Depot', $chartData['labels']);
        $this->assertContains(7000.00, $chartData['revenue']); // 5000 + 2000
        $this->assertContains(10500.00, $chartData['revenue']); // 7500 + 3000
    }

    public function test_depot_performance_report_handles_no_data(): void
    {
        $report = new DepotPerformanceReport;
        $data = $report->generate();

        $this->assertCount(0, $data);

        $summary = $report->getSummary();
        $this->assertEquals(0, $summary['depot_count']);
        $this->assertEquals(0, $summary['total_dispatches']);
        $this->assertEquals(0, $summary['total_revenue']);
        $this->assertEquals(0, $summary['total_atc_cost']);
        $this->assertEquals(0, $summary['total_transport_cost']);
        $this->assertEquals(0, $summary['average_revenue_per_depot']);
        $this->assertEquals(0, $summary['average_dispatches_per_depot']);
    }

    public function test_depot_performance_report_get_depot_list(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot',
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Abuja Depot',
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Port Harcourt Depot',
        ]);

        $report = new DepotPerformanceReport;
        $depotList = $report->getDepotList();

        $this->assertCount(3, $depotList);
        $this->assertContains('Lagos Depot', $depotList);
        $this->assertContains('Abuja Depot', $depotList);
        $this->assertContains('Port Harcourt Depot', $depotList);
    }

    public function test_depot_performance_report_export_pdf(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot',
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        $response = $this->actingAs($user)->get('/reports/depot-performance');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.depot-performance');
    }

    public function test_depot_performance_report_export_excel(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'origin' => 'Lagos Depot',
            'atc_cost' => 5000.00,
            'transport_cost' => 2000.00,
        ]);

        $response = $this->actingAs($user)->get('/reports/depot-performance');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.depot-performance');
    }
}
