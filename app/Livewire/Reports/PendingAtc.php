<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Actions\ExportPendingAtcExcel;
use App\Actions\ExportPendingAtcPdf;
use App\Reports\PendingAtcReport;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PendingAtc extends Component
{
    use WithPagination;

    public string $atcType = '';

    public ?int $status = null;

    public string $company = '';

    public function mount(): void
    {
        $this->resetPage();
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function atcTypes(): array
    {
        $report = new PendingAtcReport;

        return $report->getAtcTypes();
    }

    #[Computed]
    public function statusOptions(): array
    {
        $report = new PendingAtcReport;

        return $report->getStatusOptions();
    }

    #[Computed]
    public function reportData()
    {
        $report = new PendingAtcReport;

        return $report->generate($this->getFilters());
    }

    #[Computed]
    public function summary(): array
    {
        $report = new PendingAtcReport;

        return $report->getSummary($this->getFilters());
    }

    #[Computed]
    public function chartData(): array
    {
        $report = new PendingAtcReport;

        return $report->getChartData($this->getFilters());
    }

    public function exportReport(string $format): mixed
    {
        $filters = $this->getFilters();

        if ($format === 'pdf') {
            $exportAction = new ExportPendingAtcPdf;

            return $exportAction->execute($filters);
        }

        if ($format === 'excel') {
            return ExportPendingAtcExcel::export($filters);
        }

        return null;
    }

    private function getFilters(): array
    {
        return [
            'atc_type' => $this->atcType,
            'status' => $this->status,
            'company' => $this->company,
        ];
    }

    public function render()
    {
        return view('livewire.reports.pending-atc');
    }
}
