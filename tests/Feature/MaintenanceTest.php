<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceTest extends TestCase
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

    public function test_maintenance_index_page_loads(): void
    {
        $response = $this->get(route('maintenance.index'));
        $response->assertStatus(200);
    }

    public function test_maintenance_create_page_loads(): void
    {
        $response = $this->get(route('maintenance.create'));
        $response->assertStatus(200);
    }

    public function test_maintenance_show_page_loads(): void
    {
        $truck = Truck::factory()->create();
        $maintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
        ]);

        $response = $this->get(route('maintenance.show', $maintenance));
        $response->assertStatus(200);
    }

    public function test_maintenance_edit_page_loads(): void
    {
        $truck = Truck::factory()->create();
        $maintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
        ]);

        $response = $this->get(route('maintenance.edit', $maintenance));
        $response->assertStatus(200);
    }

    public function test_truck_maintenance_record_model_relationships(): void
    {
        $truck = Truck::factory()->create();
        $maintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
        ]);

        $this->assertInstanceOf(Truck::class, $maintenance->truck);
        $this->assertEquals($truck->id, $maintenance->truck->id);
    }

    public function test_truck_maintenance_record_scopes(): void
    {
        $truck = Truck::factory()->create();

        // Create active and inactive records
        $activeMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'status' => true,
        ]);

        $inactiveMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'status' => false,
        ]);

        // Test active scope
        $activeRecords = TruckMaintenanceRecord::active()->get();
        $this->assertTrue($activeRecords->contains($activeMaintenance));
        $this->assertFalse($activeRecords->contains($inactiveMaintenance));

        // Test inactive scope
        $inactiveRecords = TruckMaintenanceRecord::inactive()->get();
        $this->assertTrue($inactiveRecords->contains($inactiveMaintenance));
        $this->assertFalse($inactiveRecords->contains($activeMaintenance));

        // Test byTruck scope
        $truckRecords = TruckMaintenanceRecord::byTruck($truck->id)->get();
        $this->assertTrue($truckRecords->contains($activeMaintenance));
        $this->assertTrue($truckRecords->contains($inactiveMaintenance));
    }

    public function test_truck_maintenance_record_search_scope(): void
    {
        $truck = Truck::factory()->create(['registration_number' => 'GR-1234-21']);
        $maintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'description' => 'Engine oil change and filter replacement',
        ]);

        // Search by description
        $results = TruckMaintenanceRecord::search('oil change')->get();
        $this->assertTrue($results->contains($maintenance));

        // Search by truck registration
        $results = TruckMaintenanceRecord::search('GR-1234')->get();
        $this->assertTrue($results->contains($maintenance));
    }

    public function test_truck_maintenance_record_cost_range_scope(): void
    {
        $truck = Truck::factory()->create();

        $lowCostMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 500.00,
        ]);

        $highCostMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 2000.00,
        ]);

        $results = TruckMaintenanceRecord::byCostRange(1000.00, 3000.00)->get();
        $this->assertTrue($results->contains($highCostMaintenance));
        $this->assertFalse($results->contains($lowCostMaintenance));
    }

    public function test_truck_maintenance_record_date_range_scope(): void
    {
        $truck = Truck::factory()->create();

        $oldMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'created_at' => '2024-01-15',
        ]);

        $recentMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'created_at' => '2024-02-15',
        ]);

        $results = TruckMaintenanceRecord::byDateRange('2024-02-01', '2024-02-28')->get();
        $this->assertTrue($results->contains($recentMaintenance));
        $this->assertFalse($results->contains($oldMaintenance));
    }

    public function test_truck_maintenance_record_recent_scope(): void
    {
        $truck = Truck::factory()->create();

        $recentMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'created_at' => now()->subDays(10),
        ]);

        $oldMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'created_at' => now()->subDays(60),
        ]);

        $recentRecords = TruckMaintenanceRecord::recent(30)->get();
        $this->assertTrue($recentRecords->contains($recentMaintenance));
        $this->assertFalse($recentRecords->contains($oldMaintenance));
    }

    public function test_truck_maintenance_record_formatted_cost_accessor(): void
    {
        $truck = Truck::factory()->create();
        $maintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'cost_of_maintenance' => 1500.50,
        ]);

        $this->assertEquals('1,500.50', $maintenance->formatted_cost);
    }

    public function test_truck_maintenance_record_status_string_accessor(): void
    {
        $truck = Truck::factory()->create();

        $activeMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'status' => true,
        ]);

        $inactiveMaintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
            'status' => false,
        ]);

        $this->assertEquals('active', $activeMaintenance->status_string);
        $this->assertEquals('inactive', $inactiveMaintenance->status_string);
    }

    public function test_truck_maintenance_record_status_string_mutator(): void
    {
        $truck = Truck::factory()->create();
        $maintenance = TruckMaintenanceRecord::factory()->create([
            'truck_id' => $truck->id,
        ]);

        $maintenance->status_string = 'active';
        $this->assertTrue($maintenance->status);

        $maintenance->status_string = 'inactive';
        $this->assertFalse($maintenance->status);
    }
}
