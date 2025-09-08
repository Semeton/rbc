<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTest extends TestCase
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

    public function test_drivers_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/drivers');

        $response->assertStatus(200);
        $response->assertSee('Drivers');
    }

    public function test_drivers_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/drivers/create');

        $response->assertStatus(200);
        $response->assertSee('Create Driver');
    }

    public function test_driver_show_page_loads(): void
    {
        $driver = Driver::factory()->create();

        $response = $this->actingAs($this->user)->get("/drivers/{$driver->id}");

        $response->assertStatus(200);
        $response->assertSee($driver->name);
    }

    public function test_driver_edit_page_loads(): void
    {
        $driver = Driver::factory()->create();

        $response = $this->actingAs($this->user)->get("/drivers/{$driver->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Edit Driver');
    }

    public function test_driver_model_relationships(): void
    {
        $driver = Driver::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $driver->transactions());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $driver->truckRecords());
    }

    public function test_driver_scopes(): void
    {
        Driver::factory()->create(['status' => true]);
        Driver::factory()->create(['status' => false]);

        $activeDrivers = Driver::active()->get();
        $inactiveDrivers = Driver::inactive()->get();

        $this->assertCount(1, $activeDrivers);
        $this->assertCount(1, $inactiveDrivers);
    }

    public function test_driver_search_scope(): void
    {
        Driver::factory()->create(['name' => 'John Smith']);
        Driver::factory()->create(['name' => 'Jane Doe']);
        Driver::factory()->create(['company' => 'ABC Transport']);

        $searchResults = Driver::search('John')->get();
        $this->assertCount(1, $searchResults);
        $this->assertEquals('John Smith', $searchResults->first()->name);

        $companyResults = Driver::search('ABC')->get();
        $this->assertCount(1, $companyResults);
        $this->assertEquals('ABC Transport', $companyResults->first()->company);
    }

    public function test_driver_status_string_accessor(): void
    {
        $activeDriver = Driver::factory()->create(['status' => true]);
        $inactiveDriver = Driver::factory()->create(['status' => false]);

        $this->assertEquals('active', $activeDriver->status_string);
        $this->assertEquals('inactive', $inactiveDriver->status_string);
    }

    public function test_driver_initials_accessor(): void
    {
        $driver = Driver::factory()->create(['name' => 'John Smith']);

        $this->assertEquals('JS', $driver->initials);
    }
}
