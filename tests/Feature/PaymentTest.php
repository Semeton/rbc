<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
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

    public function test_payments_index_page_loads(): void
    {
        $response = $this->get(route('payments.index'));
        $response->assertStatus(200);
    }

    public function test_payments_create_page_loads(): void
    {
        $response = $this->get(route('payments.create'));
        $response->assertStatus(200);
    }

    public function test_payment_show_page_loads(): void
    {
        $customer = Customer::factory()->create();
        $payment = CustomerPayment::factory()->create(['customer_id' => $customer->id]);

        $response = $this->get(route('payments.show', $payment));
        $response->assertStatus(200);
    }

    public function test_payment_edit_page_loads(): void
    {
        $customer = Customer::factory()->create();
        $payment = CustomerPayment::factory()->create(['customer_id' => $customer->id]);

        $response = $this->get(route('payments.edit', $payment));
        $response->assertStatus(200);
    }

    public function test_customer_payment_model_relationships(): void
    {
        $customer = Customer::factory()->create();
        $payment = CustomerPayment::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(Customer::class, $payment->customer);
        $this->assertEquals($customer->id, $payment->customer->id);
    }

    public function test_customer_payment_scopes(): void
    {
        $customer = Customer::factory()->create();

        // Create payments with different dates
        $recentPayment = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'payment_date' => now()->subDays(10),
        ]);

        $oldPayment = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'payment_date' => now()->subDays(60),
        ]);

        // Test recent scope
        $recentPayments = CustomerPayment::recent(30)->get();
        $this->assertTrue($recentPayments->contains($recentPayment));
        $this->assertFalse($recentPayments->contains($oldPayment));

        // Test byCustomer scope
        $customerPayments = CustomerPayment::byCustomer($customer->id)->get();
        $this->assertTrue($customerPayments->contains($recentPayment));
        $this->assertTrue($customerPayments->contains($oldPayment));
    }

    public function test_customer_payment_search_scope(): void
    {
        $customer = Customer::factory()->create(['name' => 'John Doe']);
        $payment = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'bank_name' => 'Bank of Ghana',
            'notes' => 'Monthly payment',
        ]);

        // Search by customer name
        $results = CustomerPayment::search('John')->get();
        $this->assertTrue($results->contains($payment));

        // Search by bank name
        $results = CustomerPayment::search('Bank of Ghana')->get();
        $this->assertTrue($results->contains($payment));

        // Search by notes
        $results = CustomerPayment::search('Monthly')->get();
        $this->assertTrue($results->contains($payment));
    }

    public function test_customer_payment_formatted_amount_accessor(): void
    {
        $customer = Customer::factory()->create();
        $payment = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 15000.50,
        ]);

        $this->assertEquals('15,000.50', $payment->formatted_amount);
    }

    public function test_customer_payment_date_range_scope(): void
    {
        $customer = Customer::factory()->create();

        $payment1 = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'payment_date' => '2024-01-15',
        ]);

        $payment2 = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'payment_date' => '2024-02-15',
        ]);

        $results = CustomerPayment::byDateRange('2024-01-01', '2024-01-31')->get();
        $this->assertTrue($results->contains($payment1));
        $this->assertFalse($results->contains($payment2));
    }

    public function test_customer_payment_amount_range_scope(): void
    {
        $customer = Customer::factory()->create();

        $payment1 = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 5000.00,
        ]);

        $payment2 = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 15000.00,
        ]);

        $results = CustomerPayment::byAmountRange(1000.00, 10000.00)->get();
        $this->assertTrue($results->contains($payment1));
        $this->assertFalse($results->contains($payment2));
    }

    public function test_customer_payment_bank_scope(): void
    {
        $customer = Customer::factory()->create();

        $payment1 = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'bank_name' => 'Bank of Ghana',
        ]);

        $payment2 = CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'bank_name' => 'Ecobank',
        ]);

        $results = CustomerPayment::byBank('Ghana')->get();
        $this->assertTrue($results->contains($payment1));
        $this->assertFalse($results->contains($payment2));
    }
}
