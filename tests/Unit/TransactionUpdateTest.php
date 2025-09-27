<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Models\Atc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TransactionUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Customer $customer;
    private Driver $driver;
    private Atc $atc;
    private DailyCustomerTransaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->customer = Customer::factory()->create();
        $this->driver = Driver::factory()->create();
        $this->atc = Atc::factory()->create();
        
        $this->transaction = DailyCustomerTransaction::factory()->create([
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'atc_id' => $this->atc->id,
            'deport_details' => 'Original deport details',
        ]);
    }

    public function test_can_update_transaction_with_empty_deport_details(): void
    {
        $request = Request::create('/transactions/' . $this->transaction->id, 'PUT', [
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'atc_id' => $this->atc->id,
            'date' => '2024-01-15',
            'origin' => 'Updated Origin',
            'destination' => 'Updated Destination',
            'cement_type' => 'Updated Cement Type',
            'status' => 'active',
            'atc_cost' => 1500.00,
            'transport_cost' => 500.00,
            'deport_details' => '', // Empty string
        ]);

        $this->transaction->update([
            'customer_id' => $request->customer_id,
            'driver_id' => $request->driver_id,
            'atc_id' => $request->atc_id,
            'date' => $request->date,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'cement_type' => $request->cement_type,
            'status' => $request->status === 'active',
            'atc_cost' => $request->atc_cost,
            'transport_cost' => $request->transport_cost,
            'deport_details' => $request->deport_details ?: null,
        ]);

        $this->transaction->refresh();

        $this->assertEquals('Updated Origin', $this->transaction->origin);
        $this->assertEquals('Updated Destination', $this->transaction->destination);
        $this->assertEquals('Updated Cement Type', $this->transaction->cement_type);
        $this->assertNull($this->transaction->deport_details);
    }

    public function test_can_update_transaction_with_null_deport_details(): void
    {
        $request = Request::create('/transactions/' . $this->transaction->id, 'PUT', [
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'atc_id' => $this->atc->id,
            'date' => '2024-01-15',
            'origin' => 'Updated Origin',
            'destination' => 'Updated Destination',
            'cement_type' => 'Updated Cement Type',
            'status' => 'active',
            'atc_cost' => 1500.00,
            'transport_cost' => 500.00,
            'deport_details' => null,
        ]);

        $this->transaction->update([
            'customer_id' => $request->customer_id,
            'driver_id' => $request->driver_id,
            'atc_id' => $request->atc_id,
            'date' => $request->date,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'cement_type' => $request->cement_type,
            'status' => $request->status === 'active',
            'atc_cost' => $request->atc_cost,
            'transport_cost' => $request->transport_cost,
            'deport_details' => $request->deport_details ?: null,
        ]);

        $this->transaction->refresh();

        $this->assertEquals('Updated Origin', $this->transaction->origin);
        $this->assertEquals('Updated Destination', $this->transaction->destination);
        $this->assertEquals('Updated Cement Type', $this->transaction->cement_type);
        $this->assertNull($this->transaction->deport_details);
    }

    public function test_can_update_transaction_with_valid_deport_details(): void
    {
        $request = Request::create('/transactions/' . $this->transaction->id, 'PUT', [
            'customer_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'atc_id' => $this->atc->id,
            'date' => '2024-01-15',
            'origin' => 'Updated Origin',
            'destination' => 'Updated Destination',
            'cement_type' => 'Updated Cement Type',
            'status' => 'active',
            'atc_cost' => 1500.00,
            'transport_cost' => 500.00,
            'deport_details' => 'Updated deport details',
        ]);

        $this->transaction->update([
            'customer_id' => $request->customer_id,
            'driver_id' => $request->driver_id,
            'atc_id' => $request->atc_id,
            'date' => $request->date,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'cement_type' => $request->cement_type,
            'status' => $request->status === 'active',
            'atc_cost' => $request->atc_cost,
            'transport_cost' => $request->transport_cost,
            'deport_details' => $request->deport_details ?: null,
        ]);

        $this->transaction->refresh();

        $this->assertEquals('Updated Origin', $this->transaction->origin);
        $this->assertEquals('Updated Destination', $this->transaction->destination);
        $this->assertEquals('Updated Cement Type', $this->transaction->cement_type);
        $this->assertEquals('Updated deport details', $this->transaction->deport_details);
    }
}