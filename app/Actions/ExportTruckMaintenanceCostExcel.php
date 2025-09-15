<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\TruckMaintenanceCostReport;
use Illuminate\Support\Collection;
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

class ExportTruckMaintenanceCostExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'truck_maintenance_cost_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new TruckMaintenanceCostExcelExport($filters), $filename);
    }

    public static function export(array $filters = []): BinaryFileResponse
    {
        $filename = 'truck_maintenance_cost_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new TruckMaintenanceCostExcelExport($filters), $filename);
    }
}

class TruckMaintenanceCostExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private TruckMaintenanceCostReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new TruckMaintenanceCostReport;
    }

    public function collection(): Collection
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Truck Cab Number',
            'Truck Registration',
            'Truck Model',
            'Date',
            'Description',
            'Maintenance Cost (₦)',
            'Status',
        ];
    }

    public function map($record): array
    {
        return [
            $record['truck_cab_number'],
            $record['truck_registration_number'],
            $record['truck_model'],
            $record['date'],
            $record['description'],
            $record['maintenance_cost'],
            $record['status'],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $truckName = $this->filters['truck_id'] ? \App\Models\Truck::find($this->filters['truck_id'])->cab_number : 'All Trucks';

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 10);

        $sheet->setCellValue('A1', 'Truck Maintenance Cost Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', 'Truck Filter: '.$truckName);
        $sheet->setCellValue('A5', '');
        $sheet->setCellValue('A6', 'Summary:');
        $sheet->setCellValue('A7', 'Total Maintenance Cost: ₦'.number_format($summary['total_maintenance_cost'], 2).' | Total Records: '.number_format($summary['total_records']).' | Trucks Maintained: '.number_format($summary['unique_trucks']));
        $sheet->setCellValue('A8', 'Average Cost per Record: ₦'.number_format($summary['average_cost_per_record'], 2).' | Average Cost per Truck: ₦'.number_format($summary['average_cost_per_truck'], 2));
        $sheet->setCellValue('A9', 'Highest Maintenance Truck: '.$summary['highest_maintenance_truck'].' (₦'.number_format($summary['highest_maintenance_cost'], 2).')');

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A4')->getFont()->setSize(12);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A9')->getFont()->setBold(true);

        // Style the data headers (row 11)
        $headerRange = 'A11:G11';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A11:G'.(11 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align status column
        $sheet->getStyle('G12:G'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align cost column
        $sheet->getStyle('F12:F'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto-size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Truck Maintenance Cost';
    }
}
