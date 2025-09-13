<?php

use App\Reports\TruckUtilizationReport;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $truckId = '';

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

    public function updatedTruckId()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
        $this->truckId = '';
        $this->resetPage();
    }

    public function reportData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'truck_id' => $this->truckId ?: null,
        ];

        $report = new TruckUtilizationReport;
        return $report->generate($filters);
    }

    public function summary()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'truck_id' => $this->truckId ?: null,
        ];

        $report = new TruckUtilizationReport;
        return $report->getSummary($filters);
    }

    public function chartData()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'truck_id' => $this->truckId ?: null,
        ];

        $report = new TruckUtilizationReport;
        return $report->getChartData($filters);
    }

    public function trucks()
    {
        $report = new TruckUtilizationReport;
        return $report->getTruckList();
    }

    public function exportReport(string $format)
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'truck_id' => $this->truckId ?: null,
        ];

        if ($format === 'pdf') {
            return app(\App\Actions\ExportTruckUtilizationPdf::class)->execute($filters);
        }

        if ($format === 'excel') {
            return app(\App\Actions\ExportTruckUtilizationExcel::class)->execute($filters);
        }
    }
} ?>

<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Truck Utilization Report</flux:heading>
                <flux:subheading>Track truck usage, income generation, and maintenance costs</flux:subheading>
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
                    <flux:label>Truck</flux:label>
                    <flux:select wire:model.live="truckId" placeholder="All Trucks">
                        <option value="">All Trucks</option>
                        @foreach($this->trucks() as $truck)
                            <option value="{{ $truck->id }}">{{ $truck->cab_number }} ({{ $truck->registration_number }})</option>
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
                        <flux:icon name="truck" class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Trucks</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($this->summary()['total_trucks']) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <flux:icon name="check-circle" class="size-6 text-green-600 dark:text-green-400" />
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
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Income Generated</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['total_income_generated'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <flux:icon name="wrench-screwdriver" class="size-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Maintenance Cost</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                            ₦{{ number_format($this->summary()['total_maintenance_cost'], 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Distribution Chart -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Income Distribution by Truck</h3>
            <div class="h-80 overflow-y-auto">
                @if(count($this->chartData()['income_distribution']['labels']) > 0)
                    <div class="space-y-4">
                        @foreach($this->chartData()['income_distribution']['labels'] as $index => $cabNumber)
                            <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                <div class="flex-1">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $cabNumber }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                        ₦{{ number_format($this->chartData()['income_distribution']['income'][$index], 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <flux:icon name="chart-bar" class="mx-auto size-12 text-zinc-300 dark:text-zinc-600 mb-4" />
                            <p class="text-zinc-500 dark:text-zinc-400">No income data available for the selected criteria.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Truck Utilization Cards -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Truck Utilization Details</h3>
            </div>

            <div class="p-6 space-y-6">
                @forelse($this->reportData() as $index => $truck)
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl border border-orange-200 dark:border-orange-800 p-6 hover:shadow-lg transition-all duration-200">
                        <!-- Truck Header -->
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 p-3 bg-orange-100 dark:bg-orange-800 rounded-lg">
                                    <flux:icon name="truck" class="size-6 text-orange-600 dark:text-orange-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $truck['cab_number'] }}
                                    </h4>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                        {{ $truck['registration_number'] }} • {{ $truck['truck_model'] }} ({{ $truck['year_of_manufacture'] }})
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-center lg:text-right">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                    ₦{{ number_format($truck['total_income_generated'], 2) }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                    Total Income Generated
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Trips</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            {{ number_format($truck['total_trips']) }}
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
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Fare</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            ₦{{ number_format($truck['total_income_generated'], 2) }}
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
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Gas & Chop Money</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            ₦{{ number_format($truck['total_gas_chop_money'], 2) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg ml-3">
                                        <flux:icon name="currency-dollar" class="size-5 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Balance</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            ₦{{ number_format($truck['total_balance'], 2) }}
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
                                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Maintenance Cost</p>
                                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                            ₦{{ number_format($truck['total_maintenance_cost'], 2) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 p-2 bg-red-100 dark:bg-red-800 rounded-lg ml-3">
                                        <flux:icon name="wrench-screwdriver" class="size-5 text-red-600 dark:text-red-400" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Income Performance Bar -->
                        @if($this->reportData()->isNotEmpty())
                            @php
                                $maxIncome = $this->reportData()->max('total_income_generated');
                                $percentage = $maxIncome > 0 ? ($truck['total_income_generated'] / $maxIncome) * 100 : 0;
                            @endphp
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                                    <span class="truncate">Income vs Highest Earning Truck</span>
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
                        <flux:icon name="truck" class="mx-auto size-16 text-zinc-300 dark:text-zinc-600 mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No Truck Data Found</h3>
                        <p class="text-zinc-500 dark:text-zinc-400">No truck utilization data found for the selected criteria.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
