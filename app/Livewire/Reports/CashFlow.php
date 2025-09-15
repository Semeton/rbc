<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Actions\ExportCashFlowExcel;
use App\Actions\ExportCashFlowPdf;
use App\Reports\CashFlowReport;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CashFlow extends Component
{
    use WithPagination;

    public string $startDate = '';

    public string $endDate = '';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function reportData()
    {
        $report = new CashFlowReport;

        return $report->generate($this->getFilters());
    }

    #[Computed]
    public function summary(): array
    {
        $report = new CashFlowReport;

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData(): array
    {
        $report = new CashFlowReport;

        return $report->getChartData($this->getFilters());
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            $exportAction = new ExportCashFlowPdf;

            return $exportAction->execute($filters);
        }

        if ($format === 'excel') {
            return ExportCashFlowExcel::export($filters);
        }

        return null;
    }

    private function getFilters(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    public function render()
    {
        return view('livewire.reports.cash-flow');
    }
}
