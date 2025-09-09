<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $selectedReport = 'customer-balance';

    public string $startDate = '';

    public string $endDate = '';

    public ?int $customerId = null;

    public ?int $driverId = null;

    public ?int $truckId = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedSelectedReport(): void
    {
        $this->resetFilters();
    }

    public function resetFilters(): void
    {
        $this->customerId = null;
        $this->driverId = null;
        $this->truckId = null;
    }

    public function generateReport(): void
    {
        // This will trigger the report generation via AJAX
        $this->dispatch('generate-report', [
            'report_type' => $this->selectedReport,
            'filters' => $this->getFilters(),
        ]);
    }

    public function exportReport(string $format = 'excel'): void
    {
        $this->dispatch('export-report', [
            'report_type' => $this->selectedReport,
            'format' => $format,
            'filters' => $this->getFilters(),
        ]);
    }

    private function getFilters(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'customer_id' => $this->customerId,
            'driver_id' => $this->driverId,
            'truck_id' => $this->truckId,
        ];
    }

    #[Computed]
    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    #[Computed]
    public function drivers()
    {
        return Driver::orderBy('name')->get();
    }

    #[Computed]
    public function trucks()
    {
        return Truck::orderBy('registration_number')->get();
    }

    #[Computed]
    public function reportTypes()
    {
        return [
            'customer-balance' => [
                'name' => 'Customer Balance Report',
                'description' => 'Track customer balances, transactions, and payments',
                'icon' => 'users',
            ],
            'monthly-sales' => [
                'name' => 'Monthly Sales Report',
                'description' => 'Analyze sales performance and revenue trends',
                'icon' => 'chart-bar',
            ],
            'driver-performance' => [
                'name' => 'Driver Performance Report',
                'description' => 'Evaluate driver efficiency and performance metrics',
                'icon' => 'user-group',
            ],
            'truck-utilization' => [
                'name' => 'Truck Utilization Report',
                'description' => 'Monitor truck usage and efficiency',
                'icon' => 'truck',
            ],
            'maintenance-cost' => [
                'name' => 'Maintenance Cost Report',
                'description' => 'Track maintenance expenses and trends',
                'icon' => 'wrench-screwdriver',
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.reports.index');
    }
}
