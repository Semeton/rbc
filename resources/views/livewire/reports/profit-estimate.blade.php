<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-zinc-900 dark:text-zinc-100 sm:truncate sm:text-3xl sm:tracking-tight">
                Profit Estimate Report
            </h2>
            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                Estimate profit based on revenue and key costs: (ATC Cost + Transport Fee) – (Gas & Chop + Maintenance + Fare)
            </p>
        </div>
        <div class="flex gap-x-3">
            <flux:button primary icon="document-arrow-down" wire:click="exportReport('pdf')">
                Export PDF
            </flux:button>
            <flux:button primary icon="document-arrow-down" wire:click="exportReport('excel')">
                Export Excel
            </flux:button>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <flux:field>
            <flux:label>Start Date</flux:label>
            <flux:input
                type="date"
                wire:model.live="startDate"
                placeholder="Select start date"
            />
        </flux:field>

        <flux:field>
            <flux:label>End Date</flux:label>
            <flux:input
                type="date"
                wire:model.live="endDate"
                placeholder="Select end date"
            />
        </flux:field>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Revenue</p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                        ₦{{ number_format($this->summary['total_revenue'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-green-100 dark:bg-green-800 rounded-lg ml-3">
                    <flux:icon name="currency-dollar" class="size-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Costs</p>
                    <p class="text-lg font-semibold text-red-600 dark:text-red-400">
                        ₦{{ number_format($this->summary['total_costs'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-red-100 dark:bg-red-800 rounded-lg ml-3">
                    <flux:icon name="currency-dollar" class="size-5 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Profit</p>
                    <p class="text-lg font-semibold {{ $this->summary['total_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        ₦{{ number_format($this->summary['total_profit'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 {{ $this->summary['total_profit'] >= 0 ? 'bg-green-100 dark:bg-green-800' : 'bg-red-100 dark:bg-red-800' }} rounded-lg ml-3">
                    <flux:icon name="{{ $this->summary['total_profit'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="size-5 {{ $this->summary['total_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Profit Margin</p>
                    <p class="text-lg font-semibold {{ $this->summary['overall_profit_margin'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ number_format($this->summary['overall_profit_margin'], 2) }}%
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 {{ $this->summary['overall_profit_margin'] >= 0 ? 'bg-green-100 dark:bg-green-800' : 'bg-red-100 dark:bg-red-800' }} rounded-lg ml-3">
                    <flux:icon name="chart-bar" class="size-5 {{ $this->summary['overall_profit_margin'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Cost Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Breakdown -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Revenue Breakdown</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">ATC Cost</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->summary['total_atc_cost'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Transport Fee</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->summary['total_transport_fee'], 2) }}</span>
                </div>
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Revenue</span>
                        <span class="font-bold text-green-600 dark:text-green-400">₦{{ number_format($this->summary['total_revenue'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Cost Breakdown</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Gas & Chop</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->summary['total_gas_chop'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Fare</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->summary['total_fare'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Maintenance</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->summary['total_maintenance'], 2) }}</span>
                </div>
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Costs</span>
                        <span class="font-bold text-red-600 dark:text-red-400">₦{{ number_format($this->summary['total_costs'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Avg Profit/Day</p>
                    <p class="text-lg font-semibold {{ $this->summary['average_profit_per_day'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        ₦{{ number_format($this->summary['average_profit_per_day'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-blue-100 dark:bg-blue-800 rounded-lg ml-3">
                    <flux:icon name="calendar" class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Most Profitable Day</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $this->summary['most_profitable_day'] ? $this->summary['most_profitable_day']['date'] : 'N/A' }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-green-100 dark:bg-green-800 rounded-lg ml-3">
                    <flux:icon name="fire" class="size-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active Days</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($this->summary['active_days']) }} / {{ number_format($this->summary['total_days']) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-purple-100 dark:bg-purple-800 rounded-lg ml-3">
                    <flux:icon name="chart-bar" class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Profit Trend Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Monthly Profit Trend</h3>
                <flux:icon name="chart-bar" class="size-5 text-zinc-500" />
            </div>
            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chart-bar" class="size-12 text-zinc-400 mx-auto mb-2" />
                    <p class="text-zinc-500 dark:text-zinc-400">Monthly profit trend chart</p>
                    <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['monthly_trend']) }}</p>
                </div>
            </div>
        </div>

        <!-- Cost vs Revenue Breakdown Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Cost vs Revenue Breakdown</h3>
                <flux:icon name="chart-pie" class="size-5 text-zinc-500" />
            </div>
            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chart-pie" class="size-12 text-zinc-400 mx-auto mb-2" />
                    <p class="text-zinc-500 dark:text-zinc-400">Cost vs revenue breakdown chart</p>
                    <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['cost_breakdown']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Day
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Revenue (₦)
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Costs (₦)
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Profit (₦)
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Margin %
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->reportData as $day)
                        <tr class="{{ $day['has_activity'] ? '' : 'opacity-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $day['date_formatted'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                {{ $day['day_of_week'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                ₦{{ number_format($day['total_revenue'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                ₦{{ number_format($day['total_costs'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                <span class="{{ $day['profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $day['profit'] >= 0 ? '+' : '' }}₦{{ number_format($day['profit'], 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                <span class="{{ $day['profit_margin'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ number_format($day['profit_margin'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                No profit data found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3">
            {{-- Pagination links if needed --}}
        </div>
    </div>
</div>
