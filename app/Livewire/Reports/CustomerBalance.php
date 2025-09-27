<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\Customer;
use App\Reports\CustomerBalanceReport;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerBalance extends Component
{
    use WithPagination;

    public string $startDate = '';

    public string $endDate = '';

    public ?int $customerId = null;

    public int $perPage = 10;

    public int $chartUpdateKey = 0;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
        $this->refreshChartData();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
        $this->refreshChartData();
    }

    public function updatedCustomerId(): void
    {
        $this->resetPage();
        $this->refreshChartData();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->customerId = null;
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
        $this->refreshChartData();
    }

    private function refreshChartData(): void
    {
        // Force refresh of computed properties
        unset($this->reportData);
        unset($this->summary);
        unset($this->chartData);
        
        // Increment chart update key to force re-rendering
        $this->chartUpdateKey++;
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            return app(\App\Actions\ExportCustomerBalancePdf::class)->execute($filters);
        }

        if ($format === 'excel') {
            return app(\App\Actions\ExportCustomerBalanceExcel::class)::export($filters);
        }

        return null;
    }

    #[Computed]
    public function customers(): \Illuminate\Database\Eloquent\Collection
    {
        return Customer::orderBy('name')->get();
    }

    #[Computed]
    public function reportData(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $report = app(CustomerBalanceReport::class);

        return $report->generate($this->getFilters(), $this->perPage);
    }

    #[Computed]
    public function summary(): array
    {
        $report = app(CustomerBalanceReport::class);

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData(): array
    {
        $report = app(CustomerBalanceReport::class);

        return $report->getChartData($this->getFilters());
    }

    private function getFilters(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'customer_id' => $this->customerId,
        ];
    }

    public function render(): View
    {
        return view('livewire.reports.customer-balance');
    }
}
