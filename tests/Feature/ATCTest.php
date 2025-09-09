<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Atc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ATCTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user for authentication with admin role
        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_atcs_index_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/atcs')
            ->assertStatus(200)
            ->assertSee('ATCs');
    }

    public function test_atcs_create_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/atcs/create')
            ->assertStatus(200)
            ->assertSee('Create New ATC');
    }

    public function test_atc_show_page_loads(): void
    {
        $atc = Atc::factory()->create();

        $this->actingAs($this->user)
            ->get("/atcs/{$atc->id}")
            ->assertStatus(200)
            ->assertSee("ATC #{$atc->atc_number}");
    }

    public function test_atc_edit_page_loads(): void
    {
        $atc = Atc::factory()->create();

        $this->actingAs($this->user)
            ->get("/atcs/{$atc->id}/edit")
            ->assertStatus(200)
            ->assertSee("Edit ATC #{$atc->atc_number}");
    }

    public function test_atc_model_relationships(): void
    {
        $atc = Atc::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $atc->transactions());
    }

    public function test_atc_scopes(): void
    {
        Atc::factory()->create(['status' => true]);
        Atc::factory()->create(['status' => false]);

        $this->assertEquals(1, Atc::active()->count());
        $this->assertEquals(1, Atc::inactive()->count());
    }

    public function test_atc_search_scope(): void
    {
        $atc1 = Atc::factory()->create(['atc_number' => 12345]);
        $atc2 = Atc::factory()->create(['atc_number' => 67890]);

        $results = Atc::search('123')->get();

        $this->assertCount(1, $results);
        $this->assertEquals($atc1->id, $results->first()->id);
    }

    public function test_atc_status_string_accessor(): void
    {
        $activeAtc = Atc::factory()->create(['status' => true]);
        $inactiveAtc = Atc::factory()->create(['status' => false]);

        $this->assertEquals('active', $activeAtc->status_string);
        $this->assertEquals('inactive', $inactiveAtc->status_string);
    }

    public function test_atc_atc_type_accessor(): void
    {
        $bgAtc = Atc::factory()->create(['atc_type' => 'bg']);
        $cashAtc = Atc::factory()->create(['atc_type' => 'cash_payment']);

        $this->assertEquals('BG', $bgAtc->atc_type);
        $this->assertEquals('Cash Payment', $cashAtc->atc_type);
    }

    public function test_atc_is_available_method(): void
    {
        $atc = Atc::factory()->create(['status' => true]);

        $this->assertTrue($atc->isAvailable());
    }
}
