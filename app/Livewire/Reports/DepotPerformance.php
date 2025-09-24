<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Actions\ExportDepotPerformanceExcel;
use App\Actions\ExportDepotPerformancePdf;
use App\Reports\DepotPerformanceReport;
use App\Traits\PaginatesReportData;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DepotPerformance extends Component
{
    use WithPagination, PaginatesReportData;

    public string $startDate = '';

    public string $endDate = '';

    public ?string $depotName = null;

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

    public function updatedDepotName(): void
    {
        $this->resetPage();
        $this->refreshChartData();
    }

    public function resetFilters(): void
    {
        $this->depotName = null;
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
        $this->refreshChartData();
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            $exportAction = new ExportDepotPerformancePdf;
            return $exportAction->execute($filters);
        }

        if ($format === 'excel') {
            $exportAction = new ExportDepotPerformanceExcel;
            return $exportAction->execute($filters);
        }

        $this->dispatch('report-export-error', [
            'message' => "Unsupported export format: {$format}",
        ]);

        return null;
    }

    #[Computed]
    public function depots()
    {
        $report = app(DepotPerformanceReport::class);
        return $report->getDepotList();
    }

    #[Computed]
    public function reportData()
    {
        $report = app(DepotPerformanceReport::class);
        $data = $report->generate($this->getFilters());
        
        return $this->paginateCollection($data);
    }

    #[Computed]
    public function summary()
    {
        $report = app(DepotPerformanceReport::class);
        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData()
    {
        $report = app(DepotPerformanceReport::class);
        return $report->getChartData($this->getFilters());
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
            'depot_name' => $this->depotName,
        ];
    }

    public function render(): View
    {
        return view('livewire.reports.depot-performance');
    }
}
