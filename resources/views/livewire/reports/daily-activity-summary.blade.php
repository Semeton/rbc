<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-zinc-900 dark:text-zinc-100 sm:truncate sm:text-3xl sm:tracking-tight">
                Daily Activity Summary
            </h2>
            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                Show a daily overview of transactions and movements.
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Transactions</p>
                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                        {{ number_format($this->summary['total_transactions']) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-blue-100 dark:bg-blue-800 rounded-lg ml-3">
                    <flux:icon name="document-text" class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Sales</p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                        ₦{{ number_format($this->summary['total_sales'], 2) }}
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Payments</p>
                    <p class="text-lg font-semibold text-purple-600 dark:text-purple-400">
                        ₦{{ number_format($this->summary['total_payments_amount'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-purple-100 dark:bg-purple-800 rounded-lg ml-3">
                    <flux:icon name="currency-dollar" class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Net Activity</p>
                    <p class="text-lg font-semibold {{ $this->summary['net_activity'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        ₦{{ number_format($this->summary['net_activity'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 {{ $this->summary['net_activity'] >= 0 ? 'bg-green-100 dark:bg-green-800' : 'bg-red-100 dark:bg-red-800' }} rounded-lg ml-3">
                    <flux:icon name="{{ $this->summary['net_activity'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="size-5 {{ $this->summary['net_activity'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active Days</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($this->summary['active_days']) }} / {{ number_format($this->summary['total_days']) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg ml-3">
                    <flux:icon name="calendar" class="size-5 text-yellow-600 dark:text-yellow-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Avg Transactions/Day</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($this->summary['average_transactions_per_day'], 1) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-indigo-100 dark:bg-indigo-800 rounded-lg ml-3">
                    <flux:icon name="chart-bar" class="size-5 text-indigo-600 dark:text-indigo-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Busiest Day</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $this->summary['busiest_day'] ? $this->summary['busiest_day']['date'] : 'N/A' }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-orange-100 dark:bg-orange-800 rounded-lg ml-3">
                    <flux:icon name="fire" class="size-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Activity Trend Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Daily Activity Trend</h3>
                <flux:icon name="chart-bar" class="size-5 text-zinc-500" />
            </div>
            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chart-bar" class="size-12 text-zinc-400 mx-auto mb-2" />
                    <p class="text-zinc-500 dark:text-zinc-400">Daily activity trend chart</p>
                    <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['daily_trend']) }}</p>
                </div>
            </div>
        </div>

        <!-- Day of Week Distribution Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Day of Week Distribution</h3>
                <flux:icon name="chart-pie" class="size-5 text-zinc-500" />
            </div>
            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chart-pie" class="size-12 text-zinc-400 mx-auto mb-2" />
                    <p class="text-zinc-500 dark:text-zinc-400">Day of week distribution chart</p>
                    <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['day_of_week_distribution']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Activity Table -->
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
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Transactions
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Payments
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Total Sales (₦)
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Total Payments (₦)
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Net Activity (₦)
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $day['transaction_count'] > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $day['transaction_count'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $day['payment_count'] > 0 ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $day['payment_count'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                ₦{{ number_format($day['total_sales'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                ₦{{ number_format($day['total_payments'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                <span class="{{ $day['net_activity'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $day['net_activity'] >= 0 ? '+' : '' }}₦{{ number_format($day['net_activity'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                No daily activity data found for the selected period.
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
