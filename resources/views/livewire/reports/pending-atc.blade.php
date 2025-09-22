<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-zinc-900 dark:text-zinc-100 sm:truncate sm:text-3xl sm:tracking-tight">
                Pending ATC Report
            </h2>
            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                Show ATCs that haven't been assigned to any customer transactions.
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <flux:field>
            <flux:label>ATC Type</flux:label>
            <flux:select wire:model.live="atcType" placeholder="All Types">
                <flux:select.option value="">All Types</flux:select.option>
                @foreach($this->atcTypes as $value => $label)
                    <flux:select.option value="{{ $value }}">
                        {{ $label }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Status</flux:label>
            <flux:select wire:model.live="status" placeholder="All Statuses">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach($this->statusOptions as $value => $label)
                    <flux:select.option value="{{ $value }}">
                        {{ $label }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Company</flux:label>
            <flux:input
                wire:model.live.debounce.300ms="company"
                placeholder="Search by company name"
            />
        </flux:field>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pending ATCs</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($this->summary['total_atcs']) }}
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Value</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        ₦{{ number_format($this->summary['total_value'], 2) }}
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
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Tons</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($this->summary['total_tons']) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg ml-3">
                    <flux:icon name="scale" class="size-5 text-yellow-600 dark:text-yellow-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Active ATCs</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ number_format($this->summary['active_atcs']) }}
                    </p>
                </div>
                <div class="flex-shrink-0 p-2 bg-purple-100 dark:bg-purple-800 rounded-lg ml-3">
                    <flux:icon name="check-circle" class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- ATC Type Distribution Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">ATC Type Distribution</h3>
                <flux:icon name="chart-bar" class="size-5 text-zinc-500" />
            </div>
            @if(count($this->chartData['type_distribution']) > 0)
                <x-chart 
                    type="bar"
                    data="{{ json_encode([
                        'labels' => array_column($this->chartData['type_distribution'], 'type'),
                        'datasets' => [
                            [
                                'label' => 'Number of ATCs',
                                'data' => array_column($this->chartData['type_distribution'], 'count'),
                                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                'borderColor' => 'rgba(59, 130, 246, 1)',
                                'borderWidth' => 1
                            ]
                        ]
                    ]"
                    options="{{ json_encode([
                        'xAxisLabel' => 'ATC Types',
                        'yAxisLabel' => 'Number of ATCs',
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
                                    'stepSize' => 1
                                ]
                            ]
                        ]
                    ]"
                    height="300px"
                />
            @else
                <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="text-center">
                        <flux:icon name="chart-bar" class="size-12 text-zinc-400 mx-auto mb-2" />
                        <p class="text-zinc-500 dark:text-zinc-400">No ATC type distribution data available</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Status Distribution Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Status Distribution</h3>
                <flux:icon name="chart-pie" class="size-5 text-zinc-500" />
            </div>
            @if(count($this->chartData['status_distribution']) > 0)
                <x-chart 
                    type="doughnut"
                    data="{{ json_encode([
                        'labels' => array_column($this->chartData['status_distribution'], 'status'),
                        'datasets' => [
                            [
                                'data' => array_column($this->chartData['status_distribution'], 'count'),
                                'backgroundColor' => [
                                    'rgba(16, 185, 129, 0.8)',
                                    'rgba(239, 68, 68, 0.8)',
                                    'rgba(245, 158, 11, 0.8)',
                                    'rgba(59, 130, 246, 0.8)'
                                ],
                                'borderColor' => [
                                    'rgba(16, 185, 129, 1)',
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(245, 158, 11, 1)',
                                    'rgba(59, 130, 246, 1)'
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
                <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="text-center">
                        <flux:icon name="chart-pie" class="size-12 text-zinc-400 mx-auto mb-2" />
                        <p class="text-zinc-500 dark:text-zinc-400">No status distribution data available</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="mt-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Monthly ATC Trend</h3>
                <flux:icon name="presentation-chart-bar" class="size-5 text-zinc-500" />
            </div>
            @if(count($this->chartData['monthly_trend']) > 0)
                <x-chart 
                    type="line"
                    data="{{ json_encode([
                        'labels' => array_column($this->chartData['monthly_trend'], 'month'),
                        'datasets' => [
                            [
                                'label' => 'Number of ATCs',
                                'data' => array_column($this->chartData['monthly_trend'], 'count'),
                                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                                'borderColor' => 'rgba(59, 130, 246, 1)',
                                'borderWidth' => 3,
                                'fill' => true,
                                'tension' => 0.4
                            ]
                        ]
                    ]"
                    options="{{ json_encode([
                        'xAxisLabel' => 'Month',
                        'yAxisLabel' => 'Number of ATCs',
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
                                    'stepSize' => 1
                                ]
                            ]
                        ]
                    ]"
                    height="300px"
                />
            @else
                <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="text-center">
                        <flux:icon name="presentation-chart-bar" class="size-12 text-zinc-400 mx-auto mb-2" />
                        <p class="text-zinc-500 dark:text-zinc-400">No monthly trend data available</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            ATC Number
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            ATC Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Company
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Amount (₦)
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Tons
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Utilization
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            Created Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->reportData as $atc)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $atc['atc_number'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $atc['atc_type'] === 'bg' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $atc['atc_type_display'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $atc['company'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                ₦{{ number_format($atc['amount'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-right">
                                {{ number_format($atc['tons']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $atc['status'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $atc['status_display'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    {{ $atc['utilization_status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($atc['created_at'])->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                No pending ATCs found for the selected filters.
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
