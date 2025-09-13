<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\User;
use App\Reports\CustomerPaymentHistoryReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPaymentHistoryReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_payment_history_report_page_loads(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/reports/customer-payment-history');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.customer-payment-history');
    }

    public function test_customer_payment_history_report_generates_data(): void
    {
        $customer = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        // Create cash payment (no bank_name)
        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'payment_date' => now()->subDays(5),
            'amount' => 5000.00,
            'bank_name' => null,
        ]);

        // Create transfer payment (with bank_name)
        CustomerPayment::factory()->create([
            'customer_id' => $customer2->id,
            'payment_date' => now()->subDays(3),
            'amount' => 7500.00,
            'bank_name' => 'Bank of Ghana',
        ]);

        $report = new CustomerPaymentHistoryReport;
        $data = $report->generate();

        $this->assertCount(2, $data);

        // Check first payment (most recent)
        $firstPayment = $data->first();
        $this->assertEquals($customer2->name, $firstPayment['customer_name']);
        $this->assertEquals(7500.00, $firstPayment['amount_paid']);
        $this->assertEquals('Transfer', $firstPayment['payment_type']);
        $this->assertEquals('Bank of Ghana', $firstPayment['bank_name']);

        // Check second payment
        $secondPayment = $data->last();
        $this->assertEquals($customer->name, $secondPayment['customer_name']);
        $this->assertEquals(5000.00, $secondPayment['amount_paid']);
        $this->assertEquals('Cash', $secondPayment['payment_type']);
        $this->assertEquals('N/A', $secondPayment['bank_name']);
    }

    public function test_customer_payment_history_report_filters_by_date_range(): void
    {
        $customer = Customer::factory()->create();

        // Payment within range
        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'payment_date' => now()->subDays(10),
            'amount' => 5000.00,
        ]);

        // Payment outside range
        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'payment_date' => now()->subDays(50),
            'amount' => 3000.00,
        ]);

        $report = new CustomerPaymentHistoryReport;
        $data = $report->generate([
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(5),
        ]);

        $this->assertCount(1, $data);
        $this->assertEquals(5000.00, $data->first()['amount_paid']);
    }

    public function test_customer_payment_history_report_filters_by_customer(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        CustomerPayment::factory()->create([
            'customer_id' => $customer1->id,
            'amount' => 5000.00,
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer2->id,
            'amount' => 7500.00,
        ]);

        $report = new CustomerPaymentHistoryReport;
        $data = $report->generate(['customer_id' => $customer1->id]);

        $this->assertCount(1, $data);
        $this->assertEquals($customer1->name, $data->first()['customer_name']);
        $this->assertEquals(5000.00, $data->first()['amount_paid']);
    }

    public function test_customer_payment_history_report_filters_by_payment_type(): void
    {
        $customer = Customer::factory()->create();

        // Cash payment
        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 5000.00,
            'bank_name' => null,
        ]);

        // Transfer payment
        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 7500.00,
            'bank_name' => 'Ecobank',
        ]);

        $report = new CustomerPaymentHistoryReport;

        // Test cash filter
        $cashData = $report->generate(['payment_type' => 'cash']);
        $this->assertCount(1, $cashData);
        $this->assertEquals('Cash', $cashData->first()['payment_type']);

        // Test transfer filter
        $transferData = $report->generate(['payment_type' => 'transfer']);
        $this->assertCount(1, $transferData);
        $this->assertEquals('Transfer', $transferData->first()['payment_type']);
    }

    public function test_customer_payment_history_report_summary_calculations(): void
    {
        $customer = Customer::factory()->create();

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 5000.00,
            'bank_name' => null, // Cash
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 7500.00,
            'bank_name' => 'GCB Bank', // Transfer
        ]);

        $report = new CustomerPaymentHistoryReport;
        $summary = $report->getSummary();

        $this->assertEquals(12500.00, $summary['total_payments']);
        $this->assertEquals(2, $summary['total_transactions']);
        $this->assertEquals(5000.00, $summary['cash_payments']);
        $this->assertEquals(7500.00, $summary['transfer_payments']);
        $this->assertEquals(6250.00, $summary['average_payment']);
    }

    public function test_customer_payment_history_report_chart_data(): void
    {
        $customer = Customer::factory()->create();

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 5000.00,
            'bank_name' => null, // Cash
            'payment_date' => now()->startOfMonth(),
        ]);

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 7500.00,
            'bank_name' => 'Fidelity Bank', // Transfer
            'payment_date' => now()->startOfMonth(),
        ]);

        $report = new CustomerPaymentHistoryReport;
        $chartData = $report->getChartData();

        $this->assertArrayHasKey('payment_types', $chartData);
        $this->assertArrayHasKey('monthly_trends', $chartData);

        $this->assertContains('Cash', $chartData['payment_types']['labels']);
        $this->assertContains('Transfer', $chartData['payment_types']['labels']);

        // Check that both amounts are present (order may vary)
        $amounts = $chartData['payment_types']['amounts'];
        $this->assertContains(5000.00, $amounts);
        $this->assertContains(7500.00, $amounts);
    }

    public function test_customer_payment_history_report_handles_no_data(): void
    {
        $report = new CustomerPaymentHistoryReport;
        $data = $report->generate();

        $this->assertCount(0, $data);

        $summary = $report->getSummary();
        $this->assertEquals(0, $summary['total_payments']);
        $this->assertEquals(0, $summary['total_transactions']);
        $this->assertEquals(0, $summary['cash_payments']);
        $this->assertEquals(0, $summary['transfer_payments']);
        $this->assertEquals(0, $summary['average_payment']);
    }

    public function test_customer_payment_history_report_export_pdf(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 5000.00,
            'bank_name' => 'Bank of Ghana',
        ]);

        $response = $this->actingAs($user)->get('/reports/customer-payment-history');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.customer-payment-history');
    }

    public function test_customer_payment_history_report_export_excel(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();

        CustomerPayment::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 5000.00,
            'bank_name' => 'Bank of Ghana',
        ]);

        $response = $this->actingAs($user)->get('/reports/customer-payment-history');

        $response->assertStatus(200);
        $response->assertSeeLivewire('reports.customer-payment-history');
    }
}
