<?php

namespace App\Actions;

use App\Reports\DriverPerformanceReport;
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

class ExportDriverPerformanceExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'driver_performance_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new DriverPerformanceExcelExport($filters), $filename);
    }
}

class DriverPerformanceExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private DriverPerformanceReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new DriverPerformanceReport;
    }

    public function collection()
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Driver Name',
            'Number of Trips',
            'Total Fare Earned (₦)',
            'Avg Fare per Trip (₦)',
        ];
    }

    public function map($driver): array
    {
        $avgFarePerTrip = $driver['number_of_trips'] > 0 ? $driver['total_fare_earned'] / $driver['number_of_trips'] : 0;

        return [
            $driver['driver_name'],
            $driver['number_of_trips'],
            $driver['total_fare_earned'],
            $avgFarePerTrip,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $driverName = $this->filters['driver_id'] ? \App\Models\Driver::find($this->filters['driver_id'])->name : 'All Drivers';

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 8);

        $sheet->setCellValue('A1', 'Driver Performance Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', 'Driver Filter: '.$driverName);
        $sheet->setCellValue('A5', '');
        $sheet->setCellValue('A6', 'Summary:');
        $sheet->setCellValue('A7', 'Total Drivers: '.number_format($summary['total_drivers']).' | Total Trips: '.number_format($summary['total_trips']));
        $sheet->setCellValue('A8', 'Total Fare Earned: ₦'.number_format($summary['total_fare_earned'], 2).' | Top Performer: '.$summary['top_performer']);

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A4')->getFont()->setSize(12);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A8')->getFont()->setBold(true);

        // Style the data headers (row 9)
        $headerRange = 'A9:D9';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A9:D'.(9 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Right align numeric columns
        $numericColumns = ['B', 'C', 'D'];
        foreach ($numericColumns as $column) {
            $sheet->getStyle($column.'10:'.$column.(9 + $this->collection()->count()))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Auto-size columns
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Driver Performance';
    }
}
