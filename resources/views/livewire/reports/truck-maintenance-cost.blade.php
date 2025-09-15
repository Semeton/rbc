<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Truck Maintenance Cost Report</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Track maintenance expenses by month with trend analysis</p>
            </div>
            
            <!-- Export Buttons -->
            <div class="flex gap-2">
                <flux:button 
                    wire:click="exportReport('pdf')" 
                    variant="outline"
                    icon="document-arrow-down"
                >
                    Export PDF
                </flux:button>
                <flux:button 
                    wire:click="exportReport('excel')" 
                    variant="outline"
                    icon="table-cells"
                >
                    Export Excel
                </flux:button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                <flux:field>
                    <flux:label>Truck</flux:label>
                    <flux:select wire:model.live="truckId" placeholder="All Trucks">
                        <flux:select.option value="">All Trucks</flux:select.option>
                        @foreach($this->trucks as $truck)
                            <flux:select.option value="{{ $truck->id }}">
                                {{ $truck->cab_number }} - {{ $truck->registration_number }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Maintenance Cost</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary['total_maintenance_cost'], 2) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-800 rounded-lg ml-3">
                        <flux:icon name="currency-dollar" class="size-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Records</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary['total_records']) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-800 rounded-lg ml-3">
                        <flux:icon name="clipboard-document-list" class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Trucks Maintained</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary['unique_trucks']) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-800 rounded-lg ml-3">
                        <flux:icon name="truck" class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Avg Cost per Record</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary['average_cost_per_record'], 2) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-800 rounded-lg ml-3">
                        <flux:icon name="chart-bar" class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Trend Chart -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Monthly Maintenance Cost Trend</h3>
                    <flux:icon name="chart-bar" class="size-5 text-zinc-500" />
                </div>
                <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="text-center">
                        <flux:icon name="chart-bar" class="size-12 text-zinc-400 mx-auto mb-2" />
                        <p class="text-zinc-500 dark:text-zinc-400">Monthly trend chart</p>
                        <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['monthly_trend']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Truck Comparison Chart -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Truck Maintenance Comparison</h3>
                    <flux:icon name="chart-bar" class="size-5 text-zinc-500" />
                </div>
                <div class="h-64 flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="text-center">
                        <flux:icon name="chart-bar" class="size-12 text-zinc-400 mx-auto mb-2" />
                        <p class="text-zinc-500 dark:text-zinc-400">Truck comparison chart</p>
                        <p class="text-sm text-zinc-400">Chart data: {{ json_encode($this->chartData['truck_comparison']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Summary -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Additional Insights</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Highest Maintenance Truck</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $this->summary['highest_maintenance_truck'] }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Cost: ₦{{ number_format($this->summary['highest_maintenance_cost'], 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Average Cost per Truck</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        ₦{{ number_format($this->summary['average_cost_per_truck'], 2) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Maintenance Records Table -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Maintenance Records</h3>
</div>

            @if($this->reportData->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Truck
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Cost (₦)
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($this->reportData as $record)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                                {{ $record['truck_cab_number'] }}
                                            </p>
                                            <p class="text-sm text-zinc-500 dark:text-zinc-400 truncate">
                                                {{ $record['truck_registration_number'] }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ \Carbon\Carbon::parse($record['date'])->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-zinc-900 dark:text-zinc-100 truncate max-w-xs">
                                            {{ $record['description'] }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        ₦{{ number_format($record['maintenance_cost'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $record['status'] === 'Completed' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                            {{ $record['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <flux:icon name="clipboard-document-list" class="size-12 text-zinc-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No maintenance records found</h3>
                    <p class="text-zinc-500 dark:text-zinc-400">Try adjusting your filters to see maintenance records.</p>
                </div>
            @endif
        </div>
    </div>
</div>