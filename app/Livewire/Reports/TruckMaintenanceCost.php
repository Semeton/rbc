<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Actions\ExportTruckMaintenanceCostExcel;
use App\Actions\ExportTruckMaintenanceCostPdf;
use App\Reports\TruckMaintenanceCostReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class TruckMaintenanceCost extends Component
{
    use WithPagination;

    public string $startDate = '';

    public string $endDate = '';

    public ?int $truckId = null;

    public int $chartUpdateKey = 0;

    public function mount(): void
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfYear()->format('Y-m-d');
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

    #[Computed]
    public function trucks(): Collection
    {
        $report = new TruckMaintenanceCostReport;

        return $report->getTruckList();
    }

    #[Computed]
    public function reportData(): SupportCollection
    {
        $report = new TruckMaintenanceCostReport;

        return $report->generate($this->getFilters());
    }

    #[Computed]
    public function summary(): array
    {
        $report = new TruckMaintenanceCostReport;

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData(): array
    {
        $report = new TruckMaintenanceCostReport;

        return $report->getChartData($this->getFilters());
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            $exportAction = new ExportTruckMaintenanceCostPdf;
            return $exportAction->execute($filters);
        }

        if ($format === 'excel') {
            $exportAction = new ExportTruckMaintenanceCostExcel;
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
            'truck_id' => $this->truckId,
        ];
    }

    public function render()
    {
        return view('livewire.reports.truck-maintenance-cost');
    }
}
