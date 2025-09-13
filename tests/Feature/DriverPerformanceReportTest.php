<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Models\User;
use App\Reports\DriverPerformanceReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverPerformanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_performance_report_page_loads(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/reports/driver-performance');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.driver-performance');
    }

    public function test_driver_performance_report_generates_data(): void
    {
        $customer = Customer::factory()->create();
        $driver1 = Driver::factory()->create(['name' => 'John Doe']);
        $driver2 = Driver::factory()->create(['name' => 'Jane Smith']);
        $atc = Atc::factory()->create();

        // Create transactions for different drivers
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver1->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1, // Completed trip
            'date' => now()->subDays(5),
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver2->id,
            'atc_id' => $atc->id,
            'transport_cost' => 7500.00,
            'status' => 1, // Completed trip
            'date' => now()->subDays(3),
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver1->id,
            'atc_id' => $atc->id,
            'transport_cost' => 3000.00,
            'status' => 1, // Completed trip
            'date' => now()->subDays(2),
        ]);

        $report = new DriverPerformanceReport;
        $data = $report->generate();

        $this->assertCount(2, $data);

        // Check John Doe (should have 2 trips)
        $johnDoe = $data->firstWhere('driver_name', 'John Doe');
        $this->assertNotNull($johnDoe);
        $this->assertEquals(2, $johnDoe['number_of_trips']);
        $this->assertEquals(8000.00, $johnDoe['total_fare_earned']); // 5000 + 3000

        // Check Jane Smith
        $janeSmith = $data->firstWhere('driver_name', 'Jane Smith');
        $this->assertNotNull($janeSmith);
        $this->assertEquals(1, $janeSmith['number_of_trips']);
        $this->assertEquals(7500.00, $janeSmith['total_fare_earned']);
    }

    public function test_driver_performance_report_filters_by_date_range(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        // Transaction within range
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1,
            'date' => now()->subDays(10),
        ]);

        // Transaction outside range
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'transport_cost' => 3000.00,
            'status' => 1,
            'date' => now()->subDays(50),
        ]);

        $report = new DriverPerformanceReport;
        $data = $report->generate([
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(5),
        ]);

        $this->assertCount(1, $data);
        $this->assertEquals(5000.00, $data->first()['total_fare_earned']);
    }

    public function test_driver_performance_report_filters_by_driver(): void
    {
        $customer = Customer::factory()->create();
        $driver1 = Driver::factory()->create(['name' => 'John Doe']);
        $driver2 = Driver::factory()->create(['name' => 'Jane Smith']);
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver1->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1,
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver2->id,
            'atc_id' => $atc->id,
            'transport_cost' => 7500.00,
            'status' => 1,
        ]);

        $report = new DriverPerformanceReport;
        $data = $report->generate(['driver_id' => $driver1->id]);

        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data->first()['driver_name']);
        $this->assertEquals(5000.00, $data->first()['total_fare_earned']);
    }

    public function test_driver_performance_report_excludes_incomplete_trips(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        // Completed trip
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1, // Completed
        ]);

        // Incomplete trip
        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'transport_cost' => 3000.00,
            'status' => 0, // Incomplete
        ]);

        $report = new DriverPerformanceReport;
        $data = $report->generate();

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data->first()['number_of_trips']);
        $this->assertEquals(5000.00, $data->first()['total_fare_earned']);
    }

    public function test_driver_performance_report_summary_calculations(): void
    {
        $customer = Customer::factory()->create();
        $driver1 = Driver::factory()->create(['name' => 'John Doe']);
        $driver2 = Driver::factory()->create(['name' => 'Jane Smith']);
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver1->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1,
        ]);

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver2->id,
            'atc_id' => $atc->id,
            'transport_cost' => 7500.00,
            'status' => 1,
        ]);

        $report = new DriverPerformanceReport;
        $summary = $report->getSummary();

        $this->assertEquals(2, $summary['total_drivers']);
        $this->assertEquals(2, $summary['total_trips']);
        $this->assertEquals(12500.00, $summary['total_fare_earned']);
        $this->assertEquals(1.0, $summary['average_trips_per_driver']);
        $this->assertEquals(6250.00, $summary['average_fare_per_driver']);
        $this->assertEquals('Jane Smith', $summary['top_performer']); // Highest fare earned
    }

    public function test_driver_performance_report_chart_data(): void
    {
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1,
        ]);

        $report = new DriverPerformanceReport;
        $chartData = $report->getChartData();

        $this->assertArrayHasKey('trip_trends', $chartData);
        $this->assertArrayHasKey('top_performers', $chartData);
        $this->assertArrayHasKey('labels', $chartData['trip_trends']);
        $this->assertArrayHasKey('trips', $chartData['trip_trends']);
        $this->assertArrayHasKey('labels', $chartData['top_performers']);
        $this->assertArrayHasKey('trips', $chartData['top_performers']);
        $this->assertArrayHasKey('fare_earned', $chartData['top_performers']);
    }

    public function test_driver_performance_report_handles_no_data(): void
    {
        $report = new DriverPerformanceReport;
        $data = $report->generate();

        $this->assertCount(0, $data);

        $summary = $report->getSummary();
        $this->assertEquals(0, $summary['total_drivers']);
        $this->assertEquals(0, $summary['total_trips']);
        $this->assertEquals(0, $summary['total_fare_earned']);
        $this->assertEquals(0, $summary['average_trips_per_driver']);
        $this->assertEquals(0, $summary['average_fare_per_driver']);
        $this->assertEquals('N/A', $summary['top_performer']);
    }

    public function test_driver_performance_report_get_driver_list(): void
    {
        Driver::factory()->create(['name' => 'John Doe', 'status' => 1]);
        Driver::factory()->create(['name' => 'Jane Smith', 'status' => 1]);
        Driver::factory()->create(['name' => 'Inactive Driver', 'status' => 0]);

        $report = new DriverPerformanceReport;
        $driverList = $report->getDriverList();

        $this->assertCount(2, $driverList);
        $this->assertContains('John Doe', $driverList->pluck('name'));
        $this->assertContains('Jane Smith', $driverList->pluck('name'));
        $this->assertNotContains('Inactive Driver', $driverList->pluck('name'));
    }

    public function test_driver_performance_report_export_pdf(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/reports/driver-performance');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.driver-performance');
    }

    public function test_driver_performance_report_export_excel(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();
        $atc = Atc::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_id' => $atc->id,
            'transport_cost' => 5000.00,
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get('/reports/driver-performance');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.driver-performance');
    }
}
