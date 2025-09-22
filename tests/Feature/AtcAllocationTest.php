<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Services\AtcAllocationValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtcAllocationTest extends TestCase
{
    use RefreshDatabase;

    private AtcAllocationValidator $validator;
    private Atc $atc;
    private Customer $customer;
    private Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->validator = app(AtcAllocationValidator::class);
        
        // Create test data
        $this->atc = Atc::factory()->create([
            'tons' => 100.0,
            'atc_number' => 12345,
            'company' => 'Test Company',
        ]);
        
        $this->customer = Customer::factory()->create();
        $this->driver = Driver::factory()->create();
    }

    /** @test */
    public function it_can_calculate_remaining_tons_for_an_atc(): void
    {
        // Initially, all tons should be available
        $this->assertEquals(100.0, $this->validator->getRemainingTons($this->atc));
        
        // Create a transaction with 30 tons
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 30.0,
        ]);
        
        // Now 70 tons should be remaining
        $this->assertEquals(70.0, $this->validator->getRemainingTons($this->atc));
    }

    /** @test */
    public function it_can_validate_atc_allocation(): void
    {
        // Initially, allocation should be valid
        $this->assertTrue($this->validator->validateAtcAllocation($this->atc));
        
        // Create a transaction with 50 tons
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 50.0,
        ]);
        
        // Allocation should still be valid
        $this->assertTrue($this->validator->validateAtcAllocation($this->atc));
        
        // Create another transaction with 60 tons (total 110, exceeds 100)
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 60.0,
        ]);
        
        // Allocation should now be invalid
        $this->assertFalse($this->validator->validateAtcAllocation($this->atc));
    }

    /** @test */
    public function it_can_get_allocation_summary(): void
    {
        $summary = $this->validator->getAllocationSummary($this->atc);
        
        $this->assertEquals(100.0, $summary['total_tons']);
        $this->assertEquals(0.0, $summary['allocated_tons']);
        $this->assertEquals(100.0, $summary['remaining_tons']);
        $this->assertEquals(0.0, $summary['allocation_percentage']);
        $this->assertFalse($summary['is_fully_allocated']);
        $this->assertFalse($summary['is_over_allocated']);
        $this->assertEquals(0, $summary['transactions_count']);
    }

    /** @test */
    public function it_can_handle_multiple_customers_sharing_an_atc(): void
    {
        $customer2 = Customer::factory()->create();
        
        // Customer 1 gets 40 tons
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 40.0,
        ]);
        
        // Customer 2 gets 35 tons
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $customer2->id,
            'driver_id' => $this->driver->id,
            'tons' => 35.0,
        ]);
        
        $summary = $this->validator->getAllocationSummary($this->atc);
        
        $this->assertEquals(75.0, $summary['allocated_tons']);
        $this->assertEquals(25.0, $summary['remaining_tons']);
        $this->assertEquals(75.0, $summary['allocation_percentage']);
        $this->assertFalse($summary['is_fully_allocated']);
        $this->assertFalse($summary['is_over_allocated']);
        $this->assertEquals(2, $summary['transactions_count']);
    }

    /** @test */
    public function it_can_identify_over_allocated_atcs(): void
    {
        // Create an over-allocated ATC
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 120.0, // Exceeds the 100 tons available
        ]);
        
        $overAllocated = $this->validator->getOverAllocatedAtcs();
        
        $this->assertCount(1, $overAllocated);
        $this->assertEquals($this->atc->id, $overAllocated[0]['atc']->id);
        $this->assertTrue($overAllocated[0]['allocation']['is_over_allocated']);
    }

    /** @test */
    public function it_can_identify_fully_allocated_atcs(): void
    {
        // Create a fully allocated ATC
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 100.0, // Exactly the available tons
        ]);
        
        $fullyAllocated = $this->validator->getFullyAllocatedAtcs();
        
        $this->assertCount(1, $fullyAllocated);
        $this->assertEquals($this->atc->id, $fullyAllocated[0]['atc']->id);
        $this->assertTrue($fullyAllocated[0]['allocation']['is_fully_allocated']);
    }

    /** @test */
    public function it_can_identify_atcs_with_available_capacity(): void
    {
        // Create an ATC with available capacity
        DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 50.0, // Less than the available 100 tons
        ]);
        
        $available = $this->validator->getAtcsWithAvailableCapacity();
        
        $this->assertCount(1, $available);
        $this->assertEquals($this->atc->id, $available[0]['atc']->id);
        $this->assertFalse($available[0]['allocation']['is_fully_allocated']);
        $this->assertFalse($available[0]['allocation']['is_over_allocated']);
    }

    /** @test */
    public function it_can_exclude_transaction_from_calculation(): void
    {
        $transaction = DailyCustomerTransaction::factory()->create([
            'atc_id' => $this->atc->id,
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'tons' => 50.0,
        ]);
        
        // When excluding this transaction, all tons should be available
        $remainingTons = $this->validator->getRemainingTons($this->atc, $transaction->id);
        $this->assertEquals(100.0, $remainingTons);
        
        // When not excluding, 50 tons should be remaining
        $remainingTons = $this->validator->getRemainingTons($this->atc);
        $this->assertEquals(50.0, $remainingTons);
    }
}