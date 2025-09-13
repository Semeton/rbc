<?php

namespace App\Actions;

use App\Reports\DepotPerformanceReport;
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

class ExportDepotPerformanceExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'depot_performance_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new DepotPerformanceExcelExport($filters), $filename);
    }
}

class DepotPerformanceExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private DepotPerformanceReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new DepotPerformanceReport;
    }

    public function collection()
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Depot Name',
            'Total Dispatches',
            'ATC Cost (₦)',
            'Transport Cost (₦)',
            'Total Revenue (₦)',
        ];
    }

    public function map($depot): array
    {
        return [
            $depot['depot_name'],
            $depot['total_dispatches'],
            $depot['total_atc_cost'],
            $depot['total_transport_cost'],
            $depot['total_revenue'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $depotName = $this->filters['depot_name'] ?? 'All Depots';

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 8);

        $sheet->setCellValue('A1', 'Depot Performance Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', 'Depot Filter: '.$depotName);
        $sheet->setCellValue('A5', '');
        $sheet->setCellValue('A6', 'Summary:');
        $sheet->setCellValue('A7', 'Total Depots: '.number_format($summary['depot_count']).' | Total Dispatches: '.number_format($summary['total_dispatches']));
        $sheet->setCellValue('A8', 'Total Revenue: ₦'.number_format($summary['total_revenue'], 2).' | Avg Revenue/Depot: ₦'.number_format($summary['average_revenue_per_depot'], 2));

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A4')->getFont()->setSize(12);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A8')->getFont()->setBold(true);

        // Style the data headers (row 9)
        $headerRange = 'A9:E9';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A9:E'.(9 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Right align numeric columns
        $numericColumns = ['B', 'C', 'D', 'E'];
        foreach ($numericColumns as $column) {
            $sheet->getStyle($column.'10:'.$column.(9 + $this->collection()->count()))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Auto-size columns
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Depot Performance';
    }
}
