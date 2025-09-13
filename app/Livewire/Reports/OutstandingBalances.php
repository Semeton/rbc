<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Actions\ExportOutstandingBalancesExcel;
use App\Actions\ExportOutstandingBalancesPdf;
use App\Models\Customer;
use App\Reports\OutstandingBalancesReport;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class OutstandingBalances extends Component
{
    use WithPagination;

    public string $startDate = '';

    public string $endDate = '';

    public ?int $customerId = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
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
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            $exportAction = new ExportOutstandingBalancesPdf;

            return $exportAction->execute($filters);
        }

        if ($format === 'excel') {
            return ExportOutstandingBalancesExcel::export($filters);
        }

        $this->dispatch('report-export-error', [
            'message' => "Unsupported export format: {$format}",
        ]);

        return null;
    }

    #[Computed]
    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    #[Computed]
    public function reportData()
    {
        $report = app(OutstandingBalancesReport::class);

        return $report->generate($this->getFilters());
    }

    #[Computed]
    public function summary()
    {
        $report = app(OutstandingBalancesReport::class);

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData()
    {
        $report = app(OutstandingBalancesReport::class);

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
        return view('livewire.reports.outstanding-balances');
    }
}
