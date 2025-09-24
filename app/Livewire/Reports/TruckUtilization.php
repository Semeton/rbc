<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Actions\ExportTruckUtilizationExcel;
use App\Actions\ExportTruckUtilizationPdf;
use App\Models\Truck;
use App\Reports\TruckUtilizationReport;
use App\Traits\PaginatesReportData;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class TruckUtilization extends Component
{
    use WithPagination, PaginatesReportData;

    public string $startDate = '';

    public string $endDate = '';

    public ?int $truckId = null;

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

    public function updatedTruckId(): void
    {
        $this->resetPage();
        $this->refreshChartData();
    }

    public function resetFilters(): void
    {
        $this->truckId = null;
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
        $this->refreshChartData();
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            $exportAction = new ExportTruckUtilizationPdf;

            return $exportAction->execute($filters);
        }

        if ($format === 'excel') {
            $exportAction = new ExportTruckUtilizationExcel;
            return $exportAction->execute($filters);
        }

        $this->dispatch('report-export-error', [
            'message' => "Unsupported export format: {$format}",
        ]);

        return null;
    }

    #[Computed]
    public function trucks()
    {
        return Truck::where('status', 1)->orderBy('cab_number')->get();
    }

    #[Computed]
    public function reportData()
    {
        $report = app(TruckUtilizationReport::class);
        $data = $report->generate($this->getFilters());
        
        return $this->paginateCollection($data);
    }

    #[Computed]
    public function summary()
    {
        $report = app(TruckUtilizationReport::class);

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData()
    {
        $report = app(TruckUtilizationReport::class);

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
            'truck_id' => $this->truckId,
        ];
    }

    public function render(): View
    {
        return view('livewire.reports.truck-utilization');
    }
}
