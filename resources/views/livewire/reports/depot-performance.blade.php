<?php

use App\Reports\DepotPerformanceReport;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $depotName = '';

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

    public function updatedDepotName()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
        $this->depotName = '';
        $this->resetPage();
    }

    public function reportData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'depot_name' => $this->depotName ?: null,
        ];

        $report = new DepotPerformanceReport;
        return $report->generate($filters);
    }

    public function summary()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'depot_name' => $this->depotName ?: null,
        ];

        $report = new DepotPerformanceReport;
        return $report->getSummary($filters);
    }

    public function chartData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'depot_name' => $this->depotName ?: null,
        ];

        $report = new DepotPerformanceReport;
        return $report->getChartData($filters);
    }

    public function depotList()
    {
        $report = new DepotPerformanceReport;
        return $report->getDepotList();
    }

    public function exportReport(string $format)
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'depot_name' => $this->depotName ?: null,
        ];

        if ($format === 'pdf') {
            return app(\App\Actions\ExportDepotPerformancePdf::class)->execute($filters);
        }

        if ($format === 'excel') {
            return app(\App\Actions\ExportDepotPerformanceExcel::class)->execute($filters);
        }
    }
} ?>

<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Depot Performance Report</flux:heading>
                <flux:subheading>Analyze depot-wise activity and performance</flux:subheading>
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
                    <flux:label>Depot Name</flux:label>
                    <flux:select wire:model.live="depotName" placeholder="All Depots">
                        <option value="">All Depots</option>
                        @foreach($this->depotList() as $depot)
                            <option value="{{ $depot }}">{{ $depot }}</option>
                        @endforeach
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <flux:icon name="building-office" class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Depots</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary()['depot_count']) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <flux:icon name="truck" class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Dispatches</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary()['total_dispatches']) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <flux:icon name="currency-dollar" class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Revenue</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['total_revenue'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                        <flux:icon name="chart-bar" class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Avg Revenue/Depot</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['average_revenue_per_depot'], 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Charts -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">Depot Performance Charts</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Revenue Comparison Bar Chart -->
                <div>
                    <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Revenue by Depot</h4>
                    @if(count($this->chartData()['labels']) > 0)
                        <x-chart 
                            type="bar"
                            data="{{ json_encode([
                                'labels' => $this->chartData()['labels'],
                                'datasets' => [
                                    [
                                        'label' => 'Total Revenue',
                                        'data' => $this->chartData()['revenue'],
                                        'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                        'borderColor' => 'rgba(59, 130, 246, 1)',
                                        'borderWidth' => 1
                                    ]
                                ]
                            ]"
                            options="{{ json_encode([
                                'xAxisLabel' => 'Depots',
                                'yAxisLabel' => 'Revenue (₦)',
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
                                <p>No depot data available</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Dispatches Comparison Bar Chart -->
                <div>
                    <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Dispatches by Depot</h4>
                    @if(count($this->chartData()['labels']) > 0)
                        <x-chart 
                            type="bar"
                            data="{{ json_encode([
                                'labels' => $this->chartData()['labels'],
                                'datasets' => [
                                    [
                                        'label' => 'Total Dispatches',
                                        'data' => $this->chartData()['dispatches'],
                                        'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                        'borderColor' => 'rgba(16, 185, 129, 1)',
                                        'borderWidth' => 1
                                    ]
                                ]
                            ]"
                            options="{{ json_encode([
                                'xAxisLabel' => 'Depots',
                                'yAxisLabel' => 'Number of Dispatches',
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
                        <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                            <div class="text-center">
                                <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                                <p>No depot data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Revenue Breakdown Chart -->
            <div class="mt-8">
                <h4 class="text-md font-medium text-zinc-800 dark:text-zinc-200 mb-4">Revenue Breakdown by Depot</h4>
                @if(count($this->chartData()['labels']) > 0)
                    <x-chart 
                        type="bar"
                        data="{{ json_encode([
                            'labels' => $this->chartData()['labels'],
                            'datasets' => [
                                [
                                    'label' => 'ATC Cost',
                                    'data' => $this->chartData()['atc_costs'],
                                    'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                                    'borderColor' => 'rgba(139, 92, 246, 1)',
                                    'borderWidth' => 1
                                ],
                                [
                                    'label' => 'Transport Cost',
                                    'data' => $this->chartData()['transport_costs'],
                                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                                    'borderColor' => 'rgba(245, 158, 11, 1)',
                                    'borderWidth' => 1
                                ]
                            ]
                        ]"
                        options="{{ json_encode([
                            'xAxisLabel' => 'Depots',
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
                        height="300px"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No depot data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Depot Performance Cards -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Depot Performance Details</h3>
            </div>

            <div class="p-6 space-y-6">
                @forelse($this->reportData() as $index => $depot)
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6 hover:shadow-lg transition-all duration-200">
                        <!-- Depot Header -->
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-800 rounded-lg">
                                    <flux:icon name="building-office" class="size-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $depot['depot_name'] }}
                                    </h4>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                        Rank #{{ $index + 1 }} • {{ $depot['total_dispatches'] }} {{ Str::plural('dispatch', $depot['total_dispatches']) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-center lg:text-right">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                    ₦{{ number_format($depot['total_revenue'], 2) }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                    Total Revenue
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">ATC Revenue</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                            ₦{{ number_format($depot['total_atc_cost'], 2) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 p-2 bg-purple-100 dark:bg-purple-800 rounded-lg ml-3">
                                        <flux:icon name="cube" class="size-5 text-purple-600 dark:text-purple-400" />
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Transport Revenue</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                            ₦{{ number_format($depot['total_transport_cost'], 2) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 p-2 bg-orange-100 dark:bg-orange-800 rounded-lg ml-3">
                                        <flux:icon name="truck" class="size-5 text-orange-600 dark:text-orange-400" />
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700 sm:col-span-2 lg:col-span-1">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Avg Revenue/Dispatch</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                            ₦{{ number_format($depot['total_revenue'] / max($depot['total_dispatches'], 1), 2) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 p-2 bg-green-100 dark:bg-green-800 rounded-lg ml-3">
                                        <flux:icon name="chart-bar" class="size-5 text-green-600 dark:text-green-400" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Bar -->
                        @if($this->reportData()->isNotEmpty())
                            @php
                                $maxRevenue = $this->reportData()->max('total_revenue');
                                $percentage = $maxRevenue > 0 ? ($depot['total_revenue'] / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                                    <span class="truncate">Performance vs Best Depot</span>
                                    <span class="flex-shrink-0 ml-2">{{ number_format($percentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-3 rounded-full transition-all duration-500" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-16">
                        <flux:icon name="building-office" class="mx-auto size-16 text-zinc-300 dark:text-zinc-600 mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No Depot Data Found</h3>
                        <p class="text-zinc-500 dark:text-zinc-400">No depot performance data found for the selected criteria.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
