<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-zinc-900 dark:text-zinc-100 sm:truncate sm:text-3xl sm:tracking-tight">
                Cash Flow Report
            </h2>
            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                Monitor incoming and outgoing cash flow.
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Incoming</p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                        ₦{{ number_format($this->summary['total_incoming'], 2) }}
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Outgoing</p>
                    <p class="text-lg font-semibold text-red-600 dark:text-red-400">
                        ₦{{ number_format($this->summary['total_outgoing'], 2) }}
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Net Cash Flow</p>
                    <p class="text-lg font-semibold {{ $this->summary['net_cash_flow'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        ₦{{ number_format($this->summary['net_cash_flow'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 {{ $this->summary['net_cash_flow'] >= 0 ? 'bg-green-100 dark:bg-green-800' : 'bg-red-100 dark:bg-red-800' }} rounded-lg ml-3">
                    <flux:icon name="{{ $this->summary['net_cash_flow'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="size-5 {{ $this->summary['net_cash_flow'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Transactions</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($this->summary['incoming_count'] + $this->summary['outgoing_count']) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-blue-100 dark:bg-blue-800 rounded-lg ml-3">
                    <flux:icon name="document-text" class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Outgoing Breakdown -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Maintenance</p>
                    <p class="text-lg font-semibold text-orange-600 dark:text-orange-400">
                        ₦{{ number_format($this->summary['total_maintenance'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-orange-100 dark:bg-orange-800 rounded-lg ml-3">
                    <flux:icon name="wrench-screwdriver" class="size-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Gas & Chop</p>
                    <p class="text-lg font-semibold text-purple-600 dark:text-purple-400">
                        ₦{{ number_format($this->summary['total_gas_chop'], 2) }}
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Fare</p>
                    <p class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">
                        ₦{{ number_format($this->summary['total_fare'], 2) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-indigo-100 dark:bg-indigo-800 rounded-lg ml-3">
                    <flux:icon name="currency-dollar" class="size-5 text-indigo-600 dark:text-indigo-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Cash Flow Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Daily Cash Flow</h3>
                <flux:icon name="chart-bar" class="size-5 text-zinc-500" />
            </div>
            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chart-bar" class="size-12 text-zinc-400 mx-auto mb-2" />
                    <p class="text-zinc-500 dark:text-zinc-400">Daily cash flow chart</p>
                    <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['daily_flow']) }}</p>
                </div>
            </div>
        </div>

        <!-- Category Breakdown Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Category Breakdown</h3>
                <flux:icon name="chart-pie" class="size-5 text-zinc-500" />
            </div>
            <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chart-pie" class="size-12 text-zinc-400 mx-auto mb-2" />
                    <p class="text-zinc-500 dark:text-zinc-400">Category breakdown chart</p>
                    <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['category_breakdown']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Amount (₦)
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->reportData as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction['type'] === 'incoming' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($transaction['type']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $transaction['category'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $transaction['description'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                <span class="{{ $transaction['type'] === 'incoming' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction['type'] === 'incoming' ? '+' : '-' }}₦{{ number_format($transaction['amount'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                No cash flow transactions found for the selected period.
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
