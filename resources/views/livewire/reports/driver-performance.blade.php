<?php

use App\Reports\DriverPerformanceReport;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $driverId = '';

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

    public function updatedDriverId()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
        $this->driverId = '';
        $this->resetPage();
    }

    public function reportData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'driver_id' => $this->driverId ?: null,
        ];

        $report = new DriverPerformanceReport;
        return $report->generate($filters);
    }

    public function summary()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'driver_id' => $this->driverId ?: null,
        ];

        $report = new DriverPerformanceReport;
        return $report->getSummary($filters);
    }

    public function chartData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'driver_id' => $this->driverId ?: null,
        ];

        $report = new DriverPerformanceReport;
        return $report->getChartData($filters);
    }

    public function drivers()
    {
        $report = new DriverPerformanceReport;
        return $report->getDriverList();
    }

    public function exportReport(string $format)
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'driver_id' => $this->driverId ?: null,
        ];

        if ($format === 'pdf') {
            return app(\App\Actions\ExportDriverPerformancePdf::class)->execute($filters);
        }

        if ($format === 'excel') {
            return app(\App\Actions\ExportDriverPerformanceExcel::class)->execute($filters);
        }
    }
} ?>

<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Driver Performance Report</flux:heading>
                <flux:subheading>Track driver activity and performance per month</flux:subheading>
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
                    <flux:label>Driver</flux:label>
                    <flux:select wire:model.live="driverId" placeholder="All Drivers">
                        <option value="">All Drivers</option>
                        @foreach($this->drivers() as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
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
                        <flux:icon name="users" class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Drivers</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary()['total_drivers']) }}
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
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Trips</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary()['total_trips']) }}
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
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Fare Earned</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['total_fare_earned'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                        <flux:icon name="trophy" class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Top Performer</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                            {{ $this->summary()['top_performer'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Trip Trends Line Chart -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Trip Trends Over Time</h3>
                @if(count($this->chartData()['trip_trends']['labels']) > 0)
                    <x-chart 
                        type="line"
                        :data="[
                            'labels' => $this->chartData()['trip_trends']['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Total Trips',
                                    'data' => $this->chartData()['trip_trends']['trips'],
                                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                                    'borderColor' => 'rgba(16, 185, 129, 1)',
                                    'borderWidth' => 3,
                                    'fill' => true,
                                    'tension' => 0.4
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Month',
                            'yAxisLabel' => 'Number of Trips',
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ],
                                'tooltip' => [
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
                        height="400px"
                    />
                @else
                    <div class="h-64 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
                        <div class="text-center">
                            <flux:icon name="presentation-chart-bar" class="h-12 w-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <p>No trip trend data available for the selected period</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Top Performers Bar Chart -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Top 10 Performers</h3>
                @if(count($this->chartData()['top_performers']['labels']) > 0)
                    <x-chart 
                        type="bar"
                        :data="[
                            'labels' => $this->chartData()['top_performers']['labels'],
                            'datasets' => [
                                [
                                    'label' => 'Number of Trips',
                                    'data' => $this->chartData()['top_performers']['trips'],
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                    'borderColor' => 'rgba(59, 130, 246, 1)',
                                    'borderWidth' => 1,
                                    'yAxisID' => 'y'
                                ],
                                [
                                    'label' => 'Fare Earned (₦)',
                                    'data' => $this->chartData()['top_performers']['fare_earned'],
                                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                    'borderColor' => 'rgba(16, 185, 129, 1)',
                                    'borderWidth' => 1,
                                    'yAxisID' => 'y1'
                                ]
                            ]
                        ]"
                        :options="[
                            'xAxisLabel' => 'Drivers',
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ],
                                'tooltip' => [
                                ]
                            ],
                            'scales' => [
                                'y' => [
                                    'type' => 'linear',
                                    'display' => true,
                                    'position' => 'left',
                                    'title' => [
                                        'display' => true,
                                        'text' => 'Number of Trips'
                                    ],
                                    'beginAtZero' => true
                                ],
                                'y1' => [
                                    'type' => 'linear',
                                    'display' => true,
                                    'position' => 'right',
                                    'title' => [
                                        'display' => true,
                                        'text' => 'Fare Earned (₦)'
                                    ],
                                    'beginAtZero' => true,
                                    'grid' => [
                                        'drawOnChartArea' => false
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
                            <p>No performance data available for the selected period</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Driver Performance Cards -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Driver Performance Details</h3>
            </div>

            <div class="p-6 space-y-6">
                @forelse($this->reportData() as $index => $driver)
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800 p-6 hover:shadow-lg transition-all duration-200">
                        <!-- Driver Header -->
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-800 rounded-lg">
                                    <flux:icon name="user" class="size-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $driver['driver_name'] }}
                                    </h4>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                        Rank #{{ $index + 1 }} • {{ $driver['number_of_trips'] }} {{ Str::plural('trip', $driver['number_of_trips']) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-center lg:text-right">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                    ₦{{ number_format($driver['total_fare_earned'], 2) }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                    Total Fare Earned
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Number of Trips</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            {{ number_format($driver['number_of_trips']) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 p-2 bg-blue-100 dark:bg-blue-800 rounded-lg ml-3">
                                        <flux:icon name="truck" class="size-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Avg Fare per Trip</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            ₦{{ number_format($driver['total_fare_earned'] / max($driver['number_of_trips'], 1), 2) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 p-2 bg-purple-100 dark:bg-purple-800 rounded-lg ml-3">
                                        <flux:icon name="chart-bar" class="size-5 text-purple-600 dark:text-purple-400" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Bar -->
                        @if($this->reportData()->isNotEmpty())
                            @php
                                $maxFare = $this->reportData()->max('total_fare_earned');
                                $percentage = $maxFare > 0 ? ($driver['total_fare_earned'] / $maxFare) * 100 : 0;
                            @endphp
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                                    <span class="truncate">Performance vs Top Driver</span>
                                    <span class="flex-shrink-0 ml-2">{{ number_format($percentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-16">
                        <flux:icon name="user" class="mx-auto size-16 text-zinc-300 dark:text-zinc-600 mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No Driver Data Found</h3>
                        <p class="text-zinc-500 dark:text-zinc-400">No driver performance data found for the selected criteria.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
