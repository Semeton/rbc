<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Actions\ExportProfitEstimateExcel;
use App\Actions\ExportProfitEstimatePdf;
use App\Reports\ProfitEstimateReport;
use App\Traits\PaginatesReportData;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProfitEstimate extends Component
{
    use WithPagination, PaginatesReportData;

    public string $startDate = '';

    public string $endDate = '';

    public int $chartUpdateKey = 0;

    public function mount(): void
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
        $this->resetPage();
    }

    public function updated(): void
    {
        $this->resetPage();
        $this->refreshChartData();
    }

    #[Computed]
    public function reportData()
    {
        $report = new ProfitEstimateReport;
        $data = $report->generate($this->getFilters());
        
        return $this->paginateCollection($data);
    }

    #[Computed]
    public function summary(): array
    {
        $report = new ProfitEstimateReport;

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData(): array
    {
        $report = new ProfitEstimateReport;

        return $report->getChartData($this->getFilters());
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            $exportAction = new ExportProfitEstimatePdf;
            return $exportAction->execute($filters);
        }

        if ($format === 'excel') {
            $exportAction = new ExportProfitEstimateExcel;
            return $exportAction->execute($filters);
        }

        return null;
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

    private function getFilters(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    public function render()
    {
        return view('livewire.reports.profit-estimate');
    }
}
