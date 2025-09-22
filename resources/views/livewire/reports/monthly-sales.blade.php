<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Monthly Sales Report</flux:heading>
                <flux:subheading>Show monthly cement sales</flux:subheading>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon name="calendar" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Months</p>
                    <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($this->summary['total_months']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon name="document-text" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Transactions</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ number_format($this->summary['total_transactions']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <flux:icon name="currency-dollar" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total ATC Cost</p>
                    <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400">₦{{ number_format($this->summary['total_atc_cost'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                    <flux:icon name="truck" class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Transport Fees</p>
                    <p class="text-2xl font-semibold text-orange-600 dark:text-orange-400">₦{{ number_format($this->summary['total_transport_fees'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon name="chart-bar" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Revenue</p>
                    <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">₦{{ number_format($this->summary['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg">
                    <flux:icon name="arrow-trending-up" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Average Monthly Revenue</p>
                    <p class="text-2xl font-semibold text-indigo-600 dark:text-indigo-400">₦{{ number_format($this->summary['average_monthly_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                    <flux:icon name="star" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Best Month</p>
                    <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">
                        @if($this->summary['best_month'])
                            {{ $this->summary['best_month']['month_name'] }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Monthly Sales Data</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Month
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Total Transactions
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Total ATC Cost (₦)
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Total Transport Fees (₦)
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Total Revenue (₦)
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->reportData as $month)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $month['month_name'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($month['total_transactions']) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    ₦{{ number_format($month['total_atc_cost'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    ₦{{ number_format($month['total_transport_fees'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                    ₦{{ number_format($month['total_revenue'], 2) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-zinc-500 dark:text-zinc-400">
                                    <flux:icon name="document-text" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium mb-2">No sales data found</p>
                                    <p class="text-sm">Try adjusting your filters or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Monthly Revenue Bar Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Monthly Revenue Trend</h3>
            @if(count($this->chartData['monthly_revenue']['labels']) > 0)
                <x-chart 
                    type="bar"
                    data="{{ json_encode([
                        'labels' => $this->chartData['monthly_revenue']['labels'],
                        'datasets' => [
                            [
                                'label' => 'ATC Cost',
                                'data' => $this->chartData['monthly_revenue']['atc_cost'],
                                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                'borderColor' => 'rgba(59, 130, 246, 1)',
                                'borderWidth' => 1
                            ],
                            [
                                'label' => 'Transport Fees',
                                'data' => $this->chartData['monthly_revenue']['transport_fees'],
                                'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                'borderColor' => 'rgba(16, 185, 129, 1)',
                                'borderWidth' => 1
                            ],
                            [
                                'label' => 'Total Revenue',
                                'data' => $this->chartData['monthly_revenue']['revenue'],
                                'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                                'borderColor' => 'rgba(139, 92, 246, 1)',
                                'borderWidth' => 2,
                                'type' => 'line',
                                'fill' => false
                            ]
                        ]
                    ]"
                    options="{{ json_encode([
                        'xAxisLabel' => 'Month',
                        'yAxisLabel' => 'Amount (₦)',
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
                    height="400px"
                />
            @else
                <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                    <div class="text-center">
                        <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                        <p>No data available for the selected period</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Cement Type Distribution Pie Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Cement Type Distribution</h3>
            @if(count($this->chartData['cement_distribution']['labels']) > 0)
                <x-chart 
                    type="doughnut"
                    data="{{ json_encode([
                        'labels' => $this->chartData['cement_distribution']['labels'],
                        'datasets' => [
                            [
                                'data' => $this->chartData['cement_distribution']['transactions'],
                                'backgroundColor' => [
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(16, 185, 129, 0.8)',
                                    'rgba(139, 92, 246, 0.8)',
                                    'rgba(245, 158, 11, 0.8)',
                                    'rgba(239, 68, 68, 0.8)',
                                    'rgba(236, 72, 153, 0.8)'
                                ],
                                'borderColor' => [
                                    'rgba(59, 130, 246, 1)',
                                    'rgba(16, 185, 129, 1)',
                                    'rgba(139, 92, 246, 1)',
                                    'rgba(245, 158, 11, 1)',
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(236, 72, 153, 1)'
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
                    height="400px"
                />
            @else
                <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                    <div class="text-center">
                        <flux:icon name="chart-pie" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                        <p>No cement type data available for the selected period</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
