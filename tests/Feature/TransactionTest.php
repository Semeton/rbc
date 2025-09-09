<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_transaction_index_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/transactions');

        $response->assertStatus(200);
    }

    public function test_transaction_create_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/transactions/create');

        $response->assertStatus(200);
    }

    public function test_transaction_show_page_loads(): void
    {
        $transaction = DailyCustomerTransaction::factory()->create();

        $response = $this->actingAs($this->user)->get("/transactions/{$transaction->id}");

        $response->assertStatus(200);
    }

    public function test_transaction_edit_page_loads(): void
    {
        $transaction = DailyCustomerTransaction::factory()->create();

        $response = $this->actingAs($this->user)->get("/transactions/{$transaction->id}/edit");

        $response->assertStatus(200);
    }

    public function test_transaction_model_relationships(): void
    {
        $transaction = DailyCustomerTransaction::factory()->create();

        $this->assertInstanceOf(Customer::class, $transaction->customer);
        $this->assertInstanceOf(Driver::class, $transaction->driver);
        $this->assertInstanceOf(Atc::class, $transaction->atc);
    }

    public function test_transaction_model_scopes(): void
    {
        DailyCustomerTransaction::factory()->create(['status' => true]);
        DailyCustomerTransaction::factory()->create(['status' => false]);

        $this->assertEquals(1, DailyCustomerTransaction::active()->count());
        $this->assertEquals(1, DailyCustomerTransaction::inactive()->count());
    }

    public function test_transaction_model_accessors(): void
    {
        $activeTransaction = DailyCustomerTransaction::factory()->create(['status' => true]);
        $inactiveTransaction = DailyCustomerTransaction::factory()->create(['status' => false]);

        $this->assertEquals('active', $activeTransaction->status_string);
        $this->assertEquals('inactive', $inactiveTransaction->status_string);
    }

    public function test_transaction_model_mutators(): void
    {
        $transaction = DailyCustomerTransaction::factory()->create();

        $transaction->status_string = 'active';
        $this->assertTrue($transaction->status);

        $transaction->status_string = 'inactive';
        $this->assertFalse($transaction->status);
    }

    public function test_transaction_total_cost_accessor(): void
    {
        $transaction = DailyCustomerTransaction::factory()->create([
            'atc_cost' => 100.50,
            'transport_cost' => 50.25,
        ]);

        $this->assertEquals(150.75, $transaction->total_cost);
    }

    public function test_transaction_by_date_range_scope(): void
    {
        $transaction1 = DailyCustomerTransaction::factory()->create(['date' => '2024-01-15']);
        $transaction2 = DailyCustomerTransaction::factory()->create(['date' => '2024-01-20']);
        $transaction3 = DailyCustomerTransaction::factory()->create(['date' => '2024-02-01']);

        $results = DailyCustomerTransaction::byDateRange('2024-01-01', '2024-01-31')->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($transaction1));
        $this->assertTrue($results->contains($transaction2));
        $this->assertFalse($results->contains($transaction3));
    }

    public function test_transaction_by_customer_scope(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        $transaction1 = DailyCustomerTransaction::factory()->create(['customer_id' => $customer1->id]);
        $transaction2 = DailyCustomerTransaction::factory()->create(['customer_id' => $customer2->id]);

        $results = DailyCustomerTransaction::byCustomer($customer1->id)->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($transaction1));
        $this->assertFalse($results->contains($transaction2));
    }

    public function test_transaction_by_driver_scope(): void
    {
        $driver1 = Driver::factory()->create();
        $driver2 = Driver::factory()->create();

        $transaction1 = DailyCustomerTransaction::factory()->create(['driver_id' => $driver1->id]);
        $transaction2 = DailyCustomerTransaction::factory()->create(['driver_id' => $driver2->id]);

        $results = DailyCustomerTransaction::byDriver($driver1->id)->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($transaction1));
        $this->assertFalse($results->contains($transaction2));
    }

    public function test_transaction_by_atc_scope(): void
    {
        $atc1 = Atc::factory()->create();
        $atc2 = Atc::factory()->create();

        $transaction1 = DailyCustomerTransaction::factory()->create(['atc_id' => $atc1->id]);
        $transaction2 = DailyCustomerTransaction::factory()->create(['atc_id' => $atc2->id]);

        $results = DailyCustomerTransaction::byAtc($atc1->id)->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($transaction1));
        $this->assertFalse($results->contains($transaction2));
    }
}
