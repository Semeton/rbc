<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Depot Performance Report</flux:heading>
                <flux:subheading>Analyze depot performance and revenue</flux:subheading>
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
                <flux:label>Depot</flux:label>
                <flux:select wire:model.live="depotName" searchable>
                    <flux:select.option value="">All Depots</flux:select.option>
                    @foreach($this->depots as $depot)
                        <flux:select.option value="{{ $depot }}">{{ $depot }}</flux:select.option>
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
                    <flux:icon name="building-office" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Depots</p>
                    <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($this->summary['depot_count']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon name="truck" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Dispatches</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ number_format($this->summary['total_dispatches']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon name="currency-dollar" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Revenue</p>
                    <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">₦{{ number_format($this->summary['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <flux:icon name="chart-bar" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Avg Revenue/Depot</p>
                    <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400">₦{{ number_format($this->summary['average_revenue_per_depot'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Depot Performance Charts -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">Depot Performance Analytics</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Distribution Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Revenue Distribution by Depot</h4>
                @if(count($this->chartData['labels']) > 0)
                    <x-chart 
                        type="bar"
                        :data="[
                            'labels' => $this->chartData['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Total Revenue',
                                    'data' => $this->chartData['revenue'],
                                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                    'borderColor' => 'rgba(16, 185, 129, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Depots',
                            'yAxisLabel' => 'Revenue (₦)',
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ]
                            ],
                            'scales' => [
                                'y' => [
                                    'beginAtZero' => true
                                ]
                            ]
                        ]"
                        height="300px"
                        wire:key="revenue-distribution-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No revenue data available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Dispatch Distribution Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Dispatch Distribution by Depot</h4>
                @if(count($this->chartData['labels']) > 0)
                    <x-chart 
                        type="bar"
                        :data="[
                            'labels' => $this->chartData['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Total Dispatches',
                                    'data' => $this->chartData['dispatches'],
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                    'borderColor' => 'rgba(59, 130, 246, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Depots',
                            'yAxisLabel' => 'Number of Dispatches',
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ]
                            ],
                            'scales' => [
                                'y' => [
                                    'beginAtZero' => true
                                ]
                            ]
                        ]"
                        height="300px"
                        wire:key="dispatch-distribution-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No dispatch data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Cost Breakdown Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Cost Breakdown by Depot</h4>
                @if(count($this->chartData['labels']) > 0)
                    <x-chart 
                        type="bar"
                        :data="[
                            'labels' => $this->chartData['labels'],
                            'datasets' => [
                                [
                                    'label' => 'ATC Cost',
                                    'data' => $this->chartData['atc_costs'],
                                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                                    'borderColor' => 'rgba(245, 158, 11, 1)',
                                    'borderWidth' => 1
                                ],
                                [
                                    'label' => 'Transport Cost',
                                    'data' => $this->chartData['transport_costs'],
                                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                                    'borderColor' => 'rgba(239, 68, 68, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Depots',
                            'yAxisLabel' => 'Cost (₦)',
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ]
                            ],
                            'scales' => [
                                'y' => [
                                    'beginAtZero' => true
                                ]
                            ]
                        ]"
                        height="300px"
                        wire:key="cost-breakdown-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No cost data available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Revenue vs Dispatches Correlation -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Revenue vs Dispatches Correlation</h4>
                @if(count($this->chartData['labels']) > 0)
                    <x-chart 
                        type="scatter"
                        :data="[
                            'datasets' => [
                                [
                                    'label' => 'Revenue vs Dispatches',
                                    'data' => array_map(function($label, $revenue, $dispatches) {
                                        return ['x' => $dispatches, 'y' => $revenue];
                                    }, $this->chartData['labels'], $this->chartData['revenue'], $this->chartData['dispatches']),
                                    'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                                    'borderColor' => 'rgba(139, 92, 246, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Number of Dispatches',
                            'yAxisLabel' => 'Revenue (₦)',
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ]
                            ],
                            'scales' => [
                                'x' => [
                                    'beginAtZero' => true
                                ],
                                'y' => [
                                    'beginAtZero' => true
                                ]
                            ]
                        ]"
                        height="300px"
                        wire:key="revenue-dispatches-correlation-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No correlation data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 mt-8">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Depot Performance Details</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Depot Name
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Total Dispatches
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            ATC Cost
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Transport Cost
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Total Revenue
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->reportData as $depot)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $depot['depot_name'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($depot['total_dispatches']) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">
                                    ₦{{ number_format($depot['total_atc_cost'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-red-600 dark:text-red-400">
                                    ₦{{ number_format($depot['total_transport_cost'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                    ₦{{ number_format($depot['total_revenue'], 2) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-zinc-500 dark:text-zinc-400">
                                    <flux:icon name="building-office" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium mb-2">No depot performance data found</p>
                                    <p class="text-sm">Try adjusting your filters or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3">
            {{ $this->reportData->links() }}
        </div>
    </div>
</div>