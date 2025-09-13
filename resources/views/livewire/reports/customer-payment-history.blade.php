<?php

use App\Reports\CustomerPaymentHistoryReport;
use App\Models\Customer;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $customerId = '';
    public $paymentType = '';

    public function mount()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedCustomerId()
    {
        $this->resetPage();
    }

    public function updatedPaymentType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
        $this->customerId = '';
        $this->paymentType = '';
        $this->resetPage();
    }

    public function reportData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'customer_id' => $this->customerId ?: null,
            'payment_type' => $this->paymentType ?: null,
        ];

        $report = new CustomerPaymentHistoryReport;
        return $report->generate($filters);
    }

    public function summary()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'customer_id' => $this->customerId ?: null,
            'payment_type' => $this->paymentType ?: null,
        ];

        $report = new CustomerPaymentHistoryReport;
        return $report->getSummary($filters);
    }

    public function chartData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'customer_id' => $this->customerId ?: null,
            'payment_type' => $this->paymentType ?: null,
        ];

        $report = new CustomerPaymentHistoryReport;
        return $report->getChartData($filters);
    }

    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    public function exportReport(string $format)
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'customer_id' => $this->customerId ?: null,
            'payment_type' => $this->paymentType ?: null,
        ];

        if ($format === 'pdf') {
            return app(\App\Actions\ExportCustomerPaymentHistoryPdf::class)->execute($filters);
        }

        if ($format === 'excel') {
            return app(\App\Actions\ExportCustomerPaymentHistoryExcel::class)->execute($filters);
        }
    }
} ?>

<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Customer Payment History</flux:heading>
                <flux:subheading>Track all payments per customer</flux:subheading>
            </div>
            
            <div class="flex gap-2">
                <flux:button variant="outline" wire:click="exportReport('pdf')">
                    <flux:icon name="document-arrow-down" class="size-4" />
                    Export PDF
                </flux:button>
                <flux:button variant="outline" wire:click="exportReport('excel')">
                    <flux:icon name="document-arrow-down" class="size-4" />
                    Export Excel
                </flux:button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <flux:field>
                    <flux:label>Start Date</flux:label>
                    <flux:input type="date" wire:model.live="startDate" />
                </flux:field>

                <flux:field>
                    <flux:label>End Date</flux:label>
                    <flux:input type="date" wire:model.live="endDate" />
                </flux:field>

                <flux:field>
                    <flux:label>Customer</flux:label>
                    <flux:select wire:model.live="customerId" placeholder="All Customers">
                        <option value="">All Customers</option>
                        @foreach($this->customers() as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Payment Type</flux:label>
                    <flux:select wire:model.live="paymentType" placeholder="All Types">
                        <option value="">All Types</option>
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="mt-4">
                <flux:button variant="outline" wire:click="resetFilters">
                    <flux:icon name="arrow-path" class="size-4" />
                    Reset Filters
                </flux:button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <flux:icon name="currency-dollar" class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Payments</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['total_payments'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <flux:icon name="document-text" class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Transactions</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary()['total_transactions']) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <flux:icon name="banknotes" class="size-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Cash Payments</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['cash_payments'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <flux:icon name="credit-card" class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Transfer Payments</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['transfer_payments'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg">
                        <flux:icon name="chart-bar" class="size-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Average Payment</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['average_payment'], 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Payment Type Distribution -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Payment Type Distribution</h3>
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center">
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($this->chartData()['payment_types']['labels'] as $index => $type)
                                <div class="p-4 rounded-lg {{ $type === 'Cash' ? 'bg-yellow-100 dark:bg-yellow-900/20' : 'bg-purple-100 dark:bg-purple-900/20' }}">
                                    <div class="text-2xl font-bold {{ $type === 'Cash' ? 'text-yellow-600 dark:text-yellow-400' : 'text-purple-600 dark:text-purple-400' }}">
                                        {{ $this->chartData()['payment_types']['counts'][$index] }}
                                    </div>
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $type }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-500">
                                        ₦{{ number_format($this->chartData()['payment_types']['amounts'][$index], 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Monthly Payment Trends</h3>
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            @if(count($this->chartData()['monthly_trends']['labels']) > 0)
                                <div class="space-y-2">
                                    @foreach($this->chartData()['monthly_trends']['labels'] as $index => $month)
                                        <div class="flex justify-between items-center">
                                            <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</span>
                                            <span class="font-semibold">₦{{ number_format($this->chartData()['monthly_trends']['amounts'][$index], 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p>No data available for the selected period</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Payment History</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider min-w-[120px]">
                                Payment Date
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider min-w-[150px]">
                                Customer Name
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider min-w-[120px]">
                                Amount Paid (₦)
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider min-w-[100px]">
                                Payment Type
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider min-w-[120px]">
                                Bank Name
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($this->reportData() as $payment)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ \Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-100 break-words">
                                    <div class="max-w-[150px] truncate" title="{{ $payment['customer_name'] }}">
                                        {{ $payment['customer_name'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-100 text-right">
                                    {{ number_format($payment['amount_paid'], 2) }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <flux:badge variant="{{ $payment['payment_type'] === 'Cash' ? 'warning' : 'primary' }}">
                                        {{ $payment['payment_type'] }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-4 text-sm text-zinc-900 dark:text-zinc-100 break-words">
                                    <div class="max-w-[120px] truncate" title="{{ $payment['bank_name'] }}">
                                        {{ $payment['bank_name'] }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    <flux:icon name="document-text" class="mx-auto size-12 text-zinc-300 dark:text-zinc-600 mb-4" />
                                    <p>No payment records found for the selected criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
