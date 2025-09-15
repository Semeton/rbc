<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\PendingAtcReport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportPendingAtcExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'pending-atc-report-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new PendingAtcExcelExport($filters), $filename);
    }

    public static function export(array $filters = []): BinaryFileResponse
    {
        $filename = 'pending-atc-report-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new PendingAtcExcelExport($filters), $filename);
    }
}

class PendingAtcExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private PendingAtcReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new PendingAtcReport;
    }

    public function collection()
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'ATC Number',
            'ATC Type',
            'Company',
            'Amount (₦)',
            'Tons',
            'Status',
            'Utilization',
            'Created Date',
        ];
    }

    public function map($record): array
    {
        return [
            $record['atc_number'],
            $record['atc_type_display'],
            $record['company'],
            $record['amount'],
            $record['tons'],
            $record['status_display'],
            $record['utilization_status'],
            Carbon::parse($record['created_at'])->format('M d, Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $atcTypeFilter = $this->filters['atc_type'] ?? 'All Types';
        $statusFilter = isset($this->filters['status']) ? ($this->filters['status'] ? 'Active' : 'Inactive') : 'All Statuses';
        $companyFilter = $this->filters['company'] ?? 'All Companies';

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 10);

        $sheet->setCellValue('A1', 'Pending ATC Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'ATC Type Filter: '.$atcTypeFilter);
        $sheet->setCellValue('A4', 'Status Filter: '.$statusFilter);
        $sheet->setCellValue('A5', 'Company Filter: '.$companyFilter);
        $sheet->setCellValue('A6', '');
        $sheet->setCellValue('A7', 'Summary:');
        $sheet->setCellValue('A8', 'Total Pending ATCs: '.number_format($summary['total_atcs']).' | Total Value: ₦'.number_format($summary['total_value'], 2).' | Total Tons: '.number_format($summary['total_tons']));
        $sheet->setCellValue('A9', 'Active ATCs: '.number_format($summary['active_atcs']).' | Inactive ATCs: '.number_format($summary['inactive_atcs']).' | Utilization Rate: '.number_format($summary['utilization_rate'], 1).'%');

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A5')->getFont()->setSize(12);
        $sheet->getStyle('A7')->getFont()->setBold(true);
        $sheet->getStyle('A8:A9')->getFont()->setBold(true);

        // Style the data headers (row 11)
        $headerRange = 'A11:H11';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A11:H'.(11 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align status and utilization columns
        $sheet->getStyle('F12:F'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G12:G'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align amount and tons columns
        $sheet->getStyle('D12:D'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E12:E'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Pending ATC Report';
    }
}
