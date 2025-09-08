<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Truck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TruckTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user for authentication with admin role
        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_trucks_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/trucks');

        $response->assertStatus(200);
        $response->assertSee('Trucks');
    }

    public function test_trucks_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/trucks/create');

        $response->assertStatus(200);
        $response->assertSee('Create Truck');
    }

    public function test_truck_show_page_loads(): void
    {
        $truck = Truck::factory()->create();

        $response = $this->actingAs($this->user)->get("/trucks/{$truck->id}");

        $response->assertStatus(200);
        $response->assertSee($truck->registration_number);
    }

    public function test_truck_edit_page_loads(): void
    {
        $truck = Truck::factory()->create();

        $response = $this->actingAs($this->user)->get("/trucks/{$truck->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Edit Truck');
    }

    public function test_truck_model_relationships(): void
    {
        $truck = Truck::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $truck->maintenanceRecords());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $truck->truckRecords());
    }

    public function test_truck_scopes(): void
    {
        Truck::factory()->create(['status' => true]);
        Truck::factory()->create(['status' => false]);

        $activeTrucks = Truck::active()->get();
        $inactiveTrucks = Truck::inactive()->get();

        $this->assertCount(1, $activeTrucks);
        $this->assertCount(1, $inactiveTrucks);
    }

    public function test_truck_search_scope(): void
    {
        Truck::factory()->create(['registration_number' => 'ABC-123']);
        Truck::factory()->create(['cab_number' => 'CAB-001']);
        Truck::factory()->create(['truck_model' => 'Volvo FH16']);

        $searchResults = Truck::search('ABC')->get();
        $this->assertCount(1, $searchResults);
        $this->assertEquals('ABC-123', $searchResults->first()->registration_number);

        $modelResults = Truck::search('Volvo')->get();
        $this->assertCount(1, $modelResults);
        $this->assertEquals('Volvo FH16', $modelResults->first()->truck_model);
    }

    public function test_truck_status_string_accessor(): void
    {
        $activeTruck = Truck::factory()->create(['status' => true]);
        $inactiveTruck = Truck::factory()->create(['status' => false]);

        $this->assertEquals('active', $activeTruck->status_string);
        $this->assertEquals('inactive', $inactiveTruck->status_string);
    }

    public function test_truck_age_accessor(): void
    {
        $truck = Truck::factory()->create(['year_of_manufacture' => 2020]);

        $this->assertEquals(now()->year - 2020, $truck->age);
    }

    public function test_truck_display_name_accessor(): void
    {
        $truck = Truck::factory()->create([
            'truck_model' => 'Volvo FH16',
            'registration_number' => 'ABC-123',
        ]);

        $this->assertEquals('Volvo FH16 (ABC-123)', $truck->display_name);
    }
}