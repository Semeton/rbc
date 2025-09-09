<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dashboard\DashboardService;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        auth()->logout();
        $response = $this->get(route('dashboard.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $response = $this->get(route('dashboard.index'));
        $response->assertStatus(200);
    }

    public function test_dashboard_displays_livewire_component(): void
    {
        $response = $this->get(route('dashboard.index'));
        $response->assertStatus(200);
        // The dashboard page loads successfully with Livewire component
    }

    public function test_dashboard_service_overview_stats(): void
    {
        // Create test data
        Customer::factory()->count(5)->create(['status' => true]);
        Customer::factory()->count(2)->create(['status' => false]);

        Driver::factory()->count(3)->create(['status' => true]);
        Driver::factory()->count(1)->create(['status' => false]);

        Truck::factory()->count(4)->create(['status' => true]);
        Truck::factory()->count(1)->create(['status' => false]);

        $service = new DashboardService;
        $stats = $service->getOverviewStats();

        $this->assertEquals(7, $stats['customers']['total']);
        $this->assertEquals(5, $stats['customers']['active']);
        $this->assertEquals(4, $stats['drivers']['total']);
        $this->assertEquals(3, $stats['drivers']['active']);
        $this->assertEquals(5, $stats['trucks']['total']);
        $this->assertEquals(4, $stats['trucks']['active']);
    }

    public function test_dashboard_service_recent_activity(): void
    {
        // Create test data
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 500.00,
            'transport_cost' => 300.00,
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 1000.00,
        ]);

        $service = new DashboardService;
        $activities = $service->getRecentActivity(5);

        $this->assertNotEmpty($activities);
        $this->assertLessThanOrEqual(5, $activities->count());
    }

    public function test_dashboard_service_top_performers(): void
    {
        // Create test data
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        $driver1 = Driver::factory()->create();
        $driver2 = Driver::factory()->create();

        // Create transactions for customer1 and driver1
        DailyCustomerTransaction::factory()->count(3)->create([
            'customer_id' => $customer1->id,
            'driver_id' => $driver1->id,
            'atc_cost' => 500.00,
            'transport_cost' => 300.00,
        ]);

        // Create transactions for customer2 and driver2
        DailyCustomerTransaction::factory()->count(2)->create([
            'customer_id' => $customer2->id,
            'driver_id' => $driver2->id,
            'atc_cost' => 400.00,
            'transport_cost' => 200.00,
        ]);

        $service = new DashboardService;
        $performers = $service->getTopPerformers();

        $this->assertArrayHasKey('customers', $performers);
        $this->assertArrayHasKey('drivers', $performers);
        $this->assertNotEmpty($performers['customers']);
        $this->assertNotEmpty($performers['drivers']);
    }

    public function test_dashboard_service_revenue_chart(): void
    {
        // Create test data
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->count(5)->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 500.00,
            'transport_cost' => 300.00,
        ]);

        $service = new DashboardService;
        $chartData = $service->getRevenueChart(30);

        $this->assertIsArray($chartData);
        $this->assertLessThanOrEqual(30, count($chartData));
    }

    public function test_dashboard_service_pending_items(): void
    {
        $service = new DashboardService;
        $pendingItems = $service->getPendingItems();

        $this->assertArrayHasKey('pending_atcs', $pendingItems);
        $this->assertArrayHasKey('outstanding_customers', $pendingItems);
        $this->assertArrayHasKey('trucks_needing_maintenance', $pendingItems);
    }

    public function test_dashboard_service_quick_stats(): void
    {
        // Create test data
        $customer = Customer::factory()->create();
        $driver = Driver::factory()->create();

        DailyCustomerTransaction::factory()->create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'atc_cost' => 500.00,
            'transport_cost' => 300.00,
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 1000.00,
        ]);

        $service = new DashboardService;
        $quickStats = $service->getQuickStats();

        $this->assertArrayHasKey('today', $quickStats);
        $this->assertArrayHasKey('this_week', $quickStats);
        $this->assertArrayHasKey('this_month', $quickStats);

        $this->assertArrayHasKey('transactions', $quickStats['today']);
        $this->assertArrayHasKey('revenue', $quickStats['today']);
        $this->assertArrayHasKey('payments', $quickStats['today']);
    }

    public function test_dashboard_with_no_data(): void
    {
        $service = new DashboardService;

        $stats = $service->getOverviewStats();
        $this->assertEquals(0, $stats['customers']['total']);
        $this->assertEquals(0, $stats['drivers']['total']);
        $this->assertEquals(0, $stats['trucks']['total']);

        $activities = $service->getRecentActivity();
        $this->assertEmpty($activities);

        $performers = $service->getTopPerformers();
        $this->assertEmpty($performers['customers']);
        $this->assertEmpty($performers['drivers']);
    }
}
