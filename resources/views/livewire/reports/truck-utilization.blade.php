<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Truck Utilization Report</flux:heading>
                <flux:subheading>Monitor truck usage and efficiency</flux:subheading>
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
                <flux:label>Truck</flux:label>
                <flux:select wire:model.live="truckId" searchable>
                    <flux:select.option value="">All Trucks</flux:select.option>
                    @foreach($this->trucks as $truck)
                        <flux:select.option value="{{ $truck->id }}">{{ $truck->cab_number }} - {{ $truck->registration_number }}</flux:select.option>
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
                    <flux:icon name="truck" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Trucks</p>
                    <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($this->summary['total_trucks']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon name="chart-bar" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Trips</p>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ number_format($this->summary['total_trips']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon name="currency-dollar" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Income</p>
                    <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">₦{{ number_format($this->summary['total_income_generated'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <flux:icon name="wrench-screwdriver" class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Maintenance Cost</p>
                    <p class="text-2xl font-semibold text-red-600 dark:text-red-400">₦{{ number_format($this->summary['total_maintenance_cost'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Truck Utilization Charts -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-6">Truck Utilization Analytics</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Income Distribution Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Income Distribution by Truck</h4>
                @if(count($this->chartData['income_distribution']['labels']) > 0)
                    <x-chart 
                        type="bar"
                        :data="[
                            'labels' => $this->chartData['income_distribution']['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Income Generated',
                                    'data' => $this->chartData['income_distribution']['income'],
                                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                    'borderColor' => 'rgba(16, 185, 129, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Trucks',
                            'yAxisLabel' => 'Income (₦)',
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
                        wire:key="income-distribution-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No income data available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Trip Distribution Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Trip Distribution by Truck</h4>
                @if(count($this->chartData['trip_distribution']['labels']) > 0)
                    <x-chart 
                        type="bar"
                        :data="[
                            'labels' => $this->chartData['trip_distribution']['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Total Trips',
                                    'data' => $this->chartData['trip_distribution']['trips'],
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                    'borderColor' => 'rgba(59, 130, 246, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Trucks',
                            'yAxisLabel' => 'Number of Trips',
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
                        wire:key="trip-distribution-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No trip data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Maintenance Cost Distribution Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Maintenance Cost Distribution</h4>
                @if(count($this->chartData['maintenance_distribution']['labels']) > 0)
                    <x-chart 
                        type="bar"
                        :data="[
                            'labels' => $this->chartData['maintenance_distribution']['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Maintenance Cost',
                                    'data' => $this->chartData['maintenance_distribution']['costs'],
                                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                                    'borderColor' => 'rgba(239, 68, 68, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Trucks',
                            'yAxisLabel' => 'Maintenance Cost (₦)',
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
                        wire:key="maintenance-distribution-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No maintenance data available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Age vs Income Correlation Chart -->
            <div>
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Truck Age vs Income Correlation</h4>
                @if(count($this->chartData['age_income_correlation']['labels']) > 0)
                    <x-chart 
                        type="scatter"
                        :data="[
                            'datasets' => [
                                [
                                    'label' => 'Income vs Age',
                                    'data' => array_map(function($label, $age, $income) {
                                        return ['x' => $age, 'y' => $income];
                                    }, $this->chartData['age_income_correlation']['labels'], $this->chartData['age_income_correlation']['ages'], $this->chartData['age_income_correlation']['income']),
                                    'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                                    'borderColor' => 'rgba(139, 92, 246, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Truck Age (Years)',
                            'yAxisLabel' => 'Income (₦)',
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
                        wire:key="age-income-correlation-{{ $this->chartUpdateKey }}"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-scatter" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No age vs income data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 mt-8">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Truck Utilization Details</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Truck Details
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Trips
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Income Generated
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Maintenance Cost
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->reportData as $truck)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $truck['cab_number'] }}
                                </div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $truck['registration_number'] }} - {{ $truck['truck_model'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($truck['total_trips']) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                    ₦{{ number_format($truck['total_income_generated'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-red-600 dark:text-red-400">
                                    ₦{{ number_format($truck['total_maintenance_cost'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusColors = [
                                        'Highly Utilized' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                        'Well Utilized' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                                        'Moderately Utilized' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                        'Under Utilized' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                                        'Not Utilized' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                    ];
                                    $colorClass = $statusColors[$truck['utilization_status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                    {{ $truck['utilization_status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-zinc-500 dark:text-zinc-400">
                                    <flux:icon name="truck" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                                    <p class="text-lg font-medium mb-2">No truck utilization data found</p>
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