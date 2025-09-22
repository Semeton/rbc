<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Outstanding Balances Report</flux:heading>
                <flux:subheading>List customers who owe money</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button variant="outline" wire:click="resetFilters">
                    Reset Filters
                </flux:button>
                <flux:button variant="outline" wire:click="exportReport('pdf')">
                    <flux:icon name="document-arrow-down" />
                    Export PDF
                </flux:button>
                <flux:button variant="outline" wire:click="exportReport('excel')">
                    <flux:icon name="table-cells" />
                    Export Excel
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <flux:select wire:model.live="customerId" searchable>
                    <flux:select.option value="">All Customers</flux:select.option>
                    @foreach($this->customers as $customer)
                        <flux:select.option value="{{ $customer->id }}">{{ $customer->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Customers with Debt</p>
                    <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($this->summary['total_customers_with_debt']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <flux:icon name="currency-dollar" class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Outstanding Amount</p>
                    <p class="text-2xl font-semibold text-red-600 dark:text-red-400">${{ number_format($this->summary['total_outstanding_amount'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                    <flux:icon name="chart-bar" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Average Outstanding Amount</p>
                    <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">${{ number_format($this->summary['average_outstanding_amount'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Outstanding Balances</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Customer Name
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Last Payment Date
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Outstanding Amount
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->reportData as $customer)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $customer['customer_name'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    @if($customer['last_payment_date'])
                                        {{ \Carbon\Carbon::parse($customer['last_payment_date'])->format('M d, Y') }}
                                    @else
                                        <span class="text-zinc-500 dark:text-zinc-400">Never</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-red-600 dark:text-red-400">
                                    ${{ number_format($customer['outstanding_amount'], 2) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="text-zinc-500 dark:text-zinc-400">
                                    <flux:icon name="document-text" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium mb-2">No outstanding balances found</p>
                                    <p class="text-sm">Try adjusting your filters or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Outstanding Balances Charts -->
    <div class="mt-8 bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">Outstanding Balances Analytics</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Outstanding Amounts Bar Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Outstanding Amounts by Customer</h4>
                @if(count($this->chartData['labels']) > 0)
                    <x-chart 
                        type="bar"
                        data="{{ json_encode([
                            'labels' => $this->chartData['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Outstanding Amount',
                                    'data' => $this->chartData['outstanding_amounts'],
                                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                                    'borderColor' => 'rgba(239, 68, 68, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        options="{{ json_encode([
                            'xAxisLabel' => 'Customers',
                            'yAxisLabel' => 'Outstanding Amount (₦)',
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ],
                                'tooltip' => [
                                    'callbacks' => [
                                    ]
                                ]
                            ],
                            'scales' => [
                                'y' => [
                                    'beginAtZero' => true,
                                    'ticks' => [
                                    ]
                                ]
                            ]
                        ]"
                        height="300px"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No outstanding balances data available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Outstanding Amounts Pie Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Outstanding Balances Distribution</h4>
                @if(count($this->chartData['labels']) > 0)
                    <x-chart 
                        type="doughnut"
                        data="{{ json_encode([
                            'labels' => $this->chartData['labels'],
                            'datasets' => [
                                [
                                    'data' => $this->chartData['outstanding_amounts'],
                                    'backgroundColor' => [
                                        'rgba(239, 68, 68, 0.8)',
                                        'rgba(245, 158, 11, 0.8)',
                                        'rgba(59, 130, 246, 0.8)',
                                        'rgba(16, 185, 129, 0.8)',
                                        'rgba(139, 92, 246, 0.8)',
                                        'rgba(236, 72, 153, 0.8)',
                                        'rgba(14, 165, 233, 0.8)',
                                        'rgba(34, 197, 94, 0.8)'
                                    ],
                                    'borderColor' => [
                                        'rgba(239, 68, 68, 1)',
                                        'rgba(245, 158, 11, 1)',
                                        'rgba(59, 130, 246, 1)',
                                        'rgba(16, 185, 129, 1)',
                                        'rgba(139, 92, 246, 1)',
                                        'rgba(236, 72, 153, 1)',
                                        'rgba(14, 165, 233, 1)',
                                        'rgba(34, 197, 94, 1)'
                                    ],
                                    'borderWidth' => 2
                                ]
                            ]
                        ]"
                        options="{{ json_encode([
                            'plugins' => [
                                'legend' => [
                                    'position' => 'bottom'
                                ],
                                'tooltip' => [
                                    'callbacks' => [
                                    ]
                                ]
                            ]
                        ]"
                        height="300px"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-pie" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No outstanding balances data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>