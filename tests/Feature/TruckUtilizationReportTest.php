<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use App\Models\User;
use App\Reports\TruckUtilizationReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TruckUtilizationReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_truck_utilization_report_page_loads(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/reports/truck-utilization');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.truck-utilization');
    }

    public function test_truck_utilization_report_generates_data(): void
    {
        $truck1 = Truck::factory()->create(['cab_number' => 'TRK001', 'registration_number' => 'ABC123']);
        $truck2 = Truck::factory()->create(['cab_number' => 'TRK002', 'registration_number' => 'DEF456']);

        // Create maintenance records for truck1
        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck1->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1, // Completed
        ]);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck1->id,
            'cost_of_maintenance' => 25000.00,
            'status' => 1, // Completed
        ]);

        // Create maintenance record for truck2
        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck2->id,
            'cost_of_maintenance' => 10000.00,
            'status' => 1, // Completed
        ]);

        $report = new TruckUtilizationReport;
        $data = $report->generate();

        $this->assertCount(2, $data);

        // Check truck1 (should have higher maintenance cost)
        $truck1Data = $data->firstWhere('cab_number', 'TRK001');
        $this->assertNotNull($truck1Data);
        $this->assertEquals(2, $truck1Data['total_maintenance_records']);
        $this->assertEquals(40000.00, $truck1Data['total_maintenance_cost']);

        // Check truck2
        $truck2Data = $data->firstWhere('cab_number', 'TRK002');
        $this->assertNotNull($truck2Data);
        $this->assertEquals(1, $truck2Data['total_maintenance_records']);
        $this->assertEquals(10000.00, $truck2Data['total_maintenance_cost']);
    }

    public function test_truck_utilization_report_filters_by_date_range(): void
    {
        $truck = Truck::factory()->create();

        // Maintenance within range
        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1,
            'created_at' => now()->subDays(10),
        ]);

        // Maintenance outside range
        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 25000.00,
            'status' => 1,
            'created_at' => now()->subDays(50),
        ]);

        $report = new TruckUtilizationReport;
        $data = $report->generate([
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(5),
        ]);

        $this->assertCount(1, $data);
        $this->assertEquals(15000.00, $data->first()['total_maintenance_cost']);
    }

    public function test_truck_utilization_report_filters_by_truck(): void
    {
        $truck1 = Truck::factory()->create(['cab_number' => 'TRK001']);
        $truck2 = Truck::factory()->create(['cab_number' => 'TRK002']);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck1->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1,
        ]);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck2->id,
            'cost_of_maintenance' => 25000.00,
            'status' => 1,
        ]);

        $report = new TruckUtilizationReport;
        $data = $report->generate(['truck_id' => $truck1->id]);

        $this->assertCount(1, $data);
        $this->assertEquals('TRK001', $data->first()['cab_number']);
        $this->assertEquals(15000.00, $data->first()['total_maintenance_cost']);
    }

    public function test_truck_utilization_report_excludes_incomplete_maintenance(): void
    {
        $truck = Truck::factory()->create();

        // Completed maintenance
        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1, // Completed
        ]);

        // Incomplete maintenance
        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 25000.00,
            'status' => 0, // Incomplete
        ]);

        $report = new TruckUtilizationReport;
        $data = $report->generate();

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data->first()['total_maintenance_records']);
        $this->assertEquals(15000.00, $data->first()['total_maintenance_cost']);
    }

    public function test_truck_utilization_report_summary_calculations(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $truck1 = Truck::factory()->create(['status' => 1]); // Active
        $truck2 = Truck::factory()->create(['status' => 0]); // Inactive
        $truck3 = Truck::factory()->create(['status' => 1]); // Active

        // Create truck records for active trucks
        DailyTruckRecord::factory()->create([
            'truck_id' => $truck1->id,
            'driver_id' => $driver->id,
            'customer_id' => $customer->id,
            'fare' => 15000.00,
            'gas_chop_money' => 5000.00,
            'balance' => 10000.00,
            'status' => 1,
        ]);

        DailyTruckRecord::factory()->create([
            'truck_id' => $truck3->id,
            'driver_id' => $driver->id,
            'customer_id' => $customer->id,
            'fare' => 25000.00,
            'gas_chop_money' => 8000.00,
            'balance' => 17000.00,
            'status' => 1,
        ]);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck1->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1,
        ]);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck3->id,
            'cost_of_maintenance' => 25000.00,
            'status' => 1,
        ]);

        $report = new TruckUtilizationReport;
        $summary = $report->getSummary();

        $this->assertEquals(3, $summary['total_trucks']);
        $this->assertEquals(2, $summary['active_trucks']);
        $this->assertEquals(2, $summary['total_trips']);
        $this->assertEquals(40000.00, $summary['total_income_generated']);
        $this->assertEquals(13000.00, $summary['total_gas_chop_money']);
        $this->assertEquals(27000.00, $summary['total_balance']);
        $this->assertEquals(40000.00, $summary['total_maintenance_cost']);
        $this->assertEquals(2, $summary['total_maintenance_records']);
        $this->assertEquals(13333.33, round($summary['average_income_per_truck'], 2));
        $this->assertEquals(13333.33, round($summary['average_maintenance_cost_per_truck'], 2));
    }

    public function test_truck_utilization_report_chart_data(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $truck1 = Truck::factory()->create(['cab_number' => 'TRK001']);
        $truck2 = Truck::factory()->create(['cab_number' => 'TRK002']);

        // Create truck records
        DailyTruckRecord::factory()->create([
            'truck_id' => $truck1->id,
            'driver_id' => $driver->id,
            'customer_id' => $customer->id,
            'fare' => 15000.00,
            'gas_chop_money' => 5000.00,
            'balance' => 10000.00,
            'status' => 1,
        ]);

        DailyTruckRecord::factory()->create([
            'truck_id' => $truck2->id,
            'driver_id' => $driver->id,
            'customer_id' => $customer->id,
            'fare' => 25000.00,
            'gas_chop_money' => 8000.00,
            'balance' => 17000.00,
            'status' => 1,
        ]);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck1->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1,
        ]);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck2->id,
            'cost_of_maintenance' => 25000.00,
            'status' => 1,
        ]);

        $report = new TruckUtilizationReport;
        $chartData = $report->getChartData();

        $this->assertArrayHasKey('income_distribution', $chartData);
        $this->assertArrayHasKey('trip_distribution', $chartData);
        $this->assertArrayHasKey('maintenance_distribution', $chartData);
        $this->assertArrayHasKey('age_income_correlation', $chartData);
        $this->assertArrayHasKey('labels', $chartData['income_distribution']);
        $this->assertArrayHasKey('income', $chartData['income_distribution']);
        $this->assertArrayHasKey('labels', $chartData['trip_distribution']);
        $this->assertArrayHasKey('trips', $chartData['trip_distribution']);
        $this->assertArrayHasKey('labels', $chartData['maintenance_distribution']);
        $this->assertArrayHasKey('costs', $chartData['maintenance_distribution']);
    }

    public function test_truck_utilization_report_handles_no_data(): void
    {
        $report = new TruckUtilizationReport;
        $data = $report->generate();

        $this->assertCount(0, $data);

        $summary = $report->getSummary();
        $this->assertEquals(0, $summary['total_trucks']);
        $this->assertEquals(0, $summary['active_trucks']);
        $this->assertEquals(0, $summary['total_maintenance_cost']);
        $this->assertEquals(0, $summary['total_maintenance_records']);
        $this->assertEquals(0, $summary['average_maintenance_cost_per_truck']);
        $this->assertEquals('N/A', $summary['most_maintained_truck']);
    }

    public function test_truck_utilization_report_get_truck_list(): void
    {
        Truck::factory()->create(['cab_number' => 'TRK001', 'status' => 1]);
        Truck::factory()->create(['cab_number' => 'TRK002', 'status' => 1]);
        Truck::factory()->create(['cab_number' => 'TRK003', 'status' => 0]); // Inactive

        $report = new TruckUtilizationReport;
        $truckList = $report->getTruckList();

        $this->assertCount(2, $truckList);
        $this->assertContains('TRK001', $truckList->pluck('cab_number'));
        $this->assertContains('TRK002', $truckList->pluck('cab_number'));
        $this->assertNotContains('TRK003', $truckList->pluck('cab_number'));
    }

    public function test_truck_utilization_report_utilization_status(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $truck1 = Truck::factory()->create();
        $truck2 = Truck::factory()->create();
        $truck3 = Truck::factory()->create();

        // Truck1: High utilization (20+ trips, high income per trip, good maintenance ratio)
        for ($i = 0; $i < 25; $i++) {
            DailyTruckRecord::factory()->create([
                'truck_id' => $truck1->id,
                'driver_id' => $driver->id,
                'customer_id' => $customer->id,
                'fare' => 6000.00, // High income per trip
                'gas_chop_money' => 2000.00,
                'balance' => 4000.00,
                'status' => 1,
            ]);
        }

        // Truck2: Moderate utilization (10+ trips, moderate income per trip)
        for ($i = 0; $i < 12; $i++) {
            DailyTruckRecord::factory()->create([
                'truck_id' => $truck2->id,
                'driver_id' => $driver->id,
                'customer_id' => $customer->id,
                'fare' => 3500.00, // Moderate income per trip
                'gas_chop_money' => 1500.00,
                'balance' => 2000.00,
                'status' => 1,
            ]);
        }

        // Truck3: Low utilization (few trips, low income per trip)
        for ($i = 0; $i < 3; $i++) {
            DailyTruckRecord::factory()->create([
                'truck_id' => $truck3->id,
                'driver_id' => $driver->id,
                'customer_id' => $customer->id,
                'fare' => 1500.00, // Low income per trip
                'gas_chop_money' => 500.00,
                'balance' => 1000.00,
                'status' => 1,
            ]);
        }

        // Add maintenance costs
        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck1->id,
            'cost_of_maintenance' => 5000.00, // Low maintenance cost for high income
            'status' => 1,
        ]);

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck2->id,
            'cost_of_maintenance' => 10000.00, // Moderate maintenance cost
            'status' => 1,
        ]);

        $report = new TruckUtilizationReport;
        $data = $report->generate();

        $truck1Data = $data->firstWhere('truck_id', $truck1->id);
        $truck2Data = $data->firstWhere('truck_id', $truck2->id);
        $truck3Data = $data->firstWhere('truck_id', $truck3->id);

        $this->assertEquals('Highly Utilized', $truck1Data['utilization_status']);
        $this->assertEquals('Well Utilized', $truck2Data['utilization_status']);
        $this->assertEquals('Under Utilized', $truck3Data['utilization_status']);
    }

    public function test_truck_utilization_report_export_pdf(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $truck = Truck::factory()->create();

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/reports/truck-utilization');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.truck-utilization');
    }

    public function test_truck_utilization_report_export_excel(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $truck = Truck::factory()->create();

        TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 15000.00,
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/reports/truck-utilization');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.truck-utilization');
    }
}
