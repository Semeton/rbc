<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Models\Customer;
use App\Reports\MonthlySalesReport;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class MonthlySales extends Component
{
    use WithPagination;

    public string $startDate = '';

    public string $endDate = '';

    public ?int $customerId = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function updatedCustomerId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->customerId = null;
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
        $this->resetPage();
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            return app(\App\Actions\ExportMonthlySalesPdf::class)->execute($filters);
        }

        if ($format === 'excel') {
            return app(\App\Actions\ExportMonthlySalesExcel::class)::export($filters);
        }

        return null;
    }

    #[Computed]
    public function customers(): \Illuminate\Database\Eloquent\Collection
    {
        return Customer::orderBy('name')->get();
    }

    #[Computed]
    public function reportData(): \Illuminate\Support\Collection
    {
        $report = app(MonthlySalesReport::class);

        return $report->generate($this->getFilters());
    }

    #[Computed]
    public function summary(): array
    {
        $report = app(MonthlySalesReport::class);

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData(): array
    {
        $report = app(MonthlySalesReport::class);

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
        return view('livewire.reports.monthly-sales');
    }
}
