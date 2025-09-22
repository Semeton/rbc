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
                'route' => 'reports.customer-balance',
                'color' => 'blue',
            ],
            'monthly-sales' => [
                'name' => 'Monthly Sales Report',
                'description' => 'Analyze sales performance and revenue trends',
                'icon' => 'chart-bar',
                'route' => 'reports.monthly-sales',
                'color' => 'green',
            ],
            'customer-payment-history' => [
                'name' => 'Customer Payment History',
                'description' => 'View detailed payment history for customers',
                'icon' => 'banknotes',
                'route' => 'reports.customer-payment-history',
                'color' => 'emerald',
            ],
            'depot-performance' => [
                'name' => 'Depot Performance Report',
                'description' => 'Analyze depot performance and revenue',
                'icon' => 'building-office',
                'route' => 'reports.depot-performance',
                'color' => 'purple',
            ],
            'driver-performance' => [
                'name' => 'Driver Performance Report',
                'description' => 'Evaluate driver efficiency and performance metrics',
                'icon' => 'user-group',
                'route' => 'reports.driver-performance',
                'color' => 'orange',
            ],
            'truck-utilization' => [
                'name' => 'Truck Utilization Report',
                'description' => 'Monitor truck usage and efficiency',
                'icon' => 'truck',
                'route' => 'reports.truck-utilization',
                'color' => 'indigo',
            ],
            'outstanding-balances' => [
                'name' => 'Outstanding Balances Report',
                'description' => 'View customers with outstanding balances',
                'icon' => 'exclamation-triangle',
                'route' => 'reports.outstanding-balances',
                'color' => 'red',
            ],
            'truck-maintenance-cost' => [
                'name' => 'Truck Maintenance Cost Report',
                'description' => 'Track maintenance expenses and trends',
                'icon' => 'wrench-screwdriver',
                'route' => 'reports.truck-maintenance-cost',
                'color' => 'yellow',
            ],
            'pending-atc' => [
                'name' => 'Pending ATC Report',
                'description' => 'View unassigned ATCs awaiting customer assignment',
                'icon' => 'document-text',
                'route' => 'reports.pending-atc',
                'color' => 'amber',
            ],
            'cash-flow' => [
                'name' => 'Cash Flow Report',
                'description' => 'Monitor incoming and outgoing cash flow',
                'icon' => 'currency-dollar',
                'route' => 'reports.cash-flow',
                'color' => 'teal',
            ],
            'daily-activity-summary' => [
                'name' => 'Daily Activity Summary',
                'description' => 'Daily transaction and activity overview',
                'icon' => 'calendar-days',
                'route' => 'reports.daily-activity-summary',
                'color' => 'cyan',
            ],
            'profit-estimate' => [
                'name' => 'Profit Estimate Report',
                'description' => 'Estimate profit based on revenue and key costs',
                'icon' => 'chart-line',
                'route' => 'reports.profit-estimate',
                'color' => 'lime',
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.reports.index');
    }
}
