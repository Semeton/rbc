<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\User;
use App\Models\Atc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TruckMovementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user with admin role for middleware checks
        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($this->user);
    }

    public function test_truck_movements_index_page_loads(): void
    {
        $response = $this->get(route('truck-movements.index'));
        $response->assertStatus(200);
    }

    public function test_truck_movements_create_page_loads(): void
    {
        $response = $this->get(route('truck-movements.create'));
        $response->assertStatus(200);
    }

    public function test_truck_movement_show_page_loads(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();
        $truckMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
        ]);

        $response = $this->get(route('truck-movements.show', $truckMovement));
        $response->assertStatus(200);
    }

    public function test_truck_movement_edit_page_loads(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();
        $truckMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
        ]);

        $response = $this->get(route('truck-movements.edit', $truckMovement));
        $response->assertStatus(200);
    }

    public function test_daily_truck_record_model_relationships(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();
        $truckMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
        ]);

        $this->assertInstanceOf(Driver::class, $truckMovement->driver);
        $this->assertEquals($driver->id, $truckMovement->driver->id);

        $this->assertInstanceOf(Truck::class, $truckMovement->truck);
        $this->assertEquals($truck->id, $truckMovement->truck->id);

        $this->assertInstanceOf(Customer::class, $truckMovement->customer);
        $this->assertEquals($customer->id, $truckMovement->customer->id);
    }

    public function test_daily_truck_record_scopes(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();

        // Create active and inactive records
        $activeMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'status' => true,
        ]);

        $inactiveMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'status' => false,
        ]);

        // Test active scope
        $activeMovements = DailyTruckRecord::active()->get();
        $this->assertTrue($activeMovements->contains($activeMovement));
        $this->assertFalse($activeMovements->contains($inactiveMovement));

        // Test inactive scope
        $inactiveMovements = DailyTruckRecord::inactive()->get();
        $this->assertTrue($inactiveMovements->contains($inactiveMovement));
        $this->assertFalse($inactiveMovements->contains($activeMovement));

        // Test byDriver scope
        $driverMovements = DailyTruckRecord::byDriver($driver->id)->get();
        $this->assertTrue($driverMovements->contains($activeMovement));
        $this->assertTrue($driverMovements->contains($inactiveMovement));

        // Test byTruck scope
        $truckMovements = DailyTruckRecord::byTruck($truck->id)->get();
        $this->assertTrue($truckMovements->contains($activeMovement));
        $this->assertTrue($truckMovements->contains($inactiveMovement));

        // Test byCustomer scope
        $customerMovements = DailyTruckRecord::byCustomer($customer->id)->get();
        $this->assertTrue($customerMovements->contains($activeMovement));
        $this->assertTrue($customerMovements->contains($inactiveMovement));
    }

    public function test_daily_truck_record_search_scope(): void
    {
        $driver = Driver::factory()->create(['name' => 'John Doe']);
        $truck = Truck::factory()->create(['registration_number' => 'GR-1234-21']);
        $customer = Customer::factory()->create(['name' => 'ABC Company']);
        $truckMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
        ]);

        // Search by driver name
        $results = DailyTruckRecord::search('John')->get();
        $this->assertTrue($results->contains($truckMovement));

        // Search by truck registration
        $results = DailyTruckRecord::search('GR-1234')->get();
        $this->assertTrue($results->contains($truckMovement));

        // Search by customer name
        $results = DailyTruckRecord::search('ABC Company')->get();
        $this->assertTrue($results->contains($truckMovement));
    }

    public function test_daily_truck_record_net_profit_accessor(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();
        $truckMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'fare' => 15000.00,
            'gas_chop_money' => 3000.00,
        ]);

        $this->assertEquals(12000.00, $truckMovement->net_profit);
    }

    public function test_financial_totals_are_calculated_correctly(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();
        $atc = Atc::factory()->create([
            'amount' => 5000.00,
        ]);

        $movement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'atc_id' => $atc->id,
            'customer_cost' => 15000.00,
            'fare' => 10000.00,
            'gas_chop_money' => 3000.00,
            'haulage' => 2000.00,
            'incentive' => 1000.00,
        ]);

        $this->assertEquals(10000.00, $movement->fare);
        $this->assertEquals(9000.00, $movement->total);
        $this->assertEquals(10000.00, $movement->total_plus_incentive);
    }

    public function test_daily_truck_record_status_string_accessor(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();

        $activeMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'status' => true,
        ]);

        $inactiveMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'status' => false,
        ]);

        $this->assertEquals('active', $activeMovement->status_string);
        $this->assertEquals('inactive', $inactiveMovement->status_string);
    }

    public function test_daily_truck_record_status_string_mutator(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();
        $truckMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
        ]);

        $truckMovement->status_string = 'active';
        $this->assertTrue($truckMovement->status);

        $truckMovement->status_string = 'inactive';
        $this->assertFalse($truckMovement->status);
    }

    public function test_daily_truck_record_date_range_scope(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();

        $movement1 = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'atc_collection_date' => '2024-01-15',
        ]);

        $movement2 = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'atc_collection_date' => '2024-02-15',
        ]);

        $results = DailyTruckRecord::byDateRange('2024-01-01', '2024-01-31')->get();
        $this->assertTrue($results->contains($movement1));
        $this->assertFalse($results->contains($movement2));
    }

    public function test_daily_truck_record_recent_scope(): void
    {
        $driver = Driver::factory()->create();
        $truck = Truck::factory()->create();
        $customer = Customer::factory()->create();

        $recentMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'atc_collection_date' => now()->subDays(10),
        ]);

        $oldMovement = DailyTruckRecord::factory()->create([
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'customer_id' => $customer->id,
            'atc_collection_date' => now()->subDays(60),
        ]);

        $recentMovements = DailyTruckRecord::recent(30)->get();
        $this->assertTrue($recentMovements->contains($recentMovement));
        $this->assertFalse($recentMovements->contains($oldMovement));
    }
}
