<?php

namespace App\Actions;

use App\Reports\TruckUtilizationReport;
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

class ExportTruckUtilizationExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'truck_utilization_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new TruckUtilizationExcelExport($filters), $filename);
    }
}

class TruckUtilizationExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private TruckUtilizationReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new TruckUtilizationReport;
    }

    public function collection()
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Cab Number',
            'Registration Number',
            'Truck Model',
            'Year',
            'Status',
            'Total Trips',
            'Total Fare (₦)',
            'Gas & Chop Money (₦)',
            'Balance (₦)',
            'Maintenance Cost (₦)',
            'Maintenance Records',
            'Utilization Status',
        ];
    }

    public function map($truck): array
    {
        return [
            $truck['cab_number'],
            $truck['registration_number'],
            $truck['truck_model'],
            $truck['year_of_manufacture'],
            $truck['truck_status'] ? 'Active' : 'Inactive',
            $truck['total_trips'],
            $truck['total_income_generated'],
            $truck['total_gas_chop_money'],
            $truck['total_balance'],
            $truck['total_maintenance_cost'],
            $truck['total_maintenance_records'],
            $truck['utilization_status'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $truckName = $this->filters['truck_id'] ? \App\Models\Truck::find($this->filters['truck_id'])->cab_number : 'All Trucks';

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 10);

        $sheet->setCellValue('A1', 'Truck Utilization Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', 'Truck Filter: '.$truckName);
        $sheet->setCellValue('A5', '');
        $sheet->setCellValue('A6', 'Summary:');
        $sheet->setCellValue('A7', 'Total Trucks: '.number_format($summary['total_trucks']).' | Active Trucks: '.number_format($summary['active_trucks']).' | Total Trips: '.number_format($summary['total_trips']));
        $sheet->setCellValue('A8', 'Total Fare: ₦'.number_format($summary['total_income_generated'], 2).' | Total Gas & Chop: ₦'.number_format($summary['total_gas_chop_money'], 2).' | Total Balance: ₦'.number_format($summary['total_balance'], 2));
        $sheet->setCellValue('A9', 'Total Maintenance Cost: ₦'.number_format($summary['total_maintenance_cost'], 2).' | Average Income per Truck: ₦'.number_format($summary['average_income_per_truck'], 2));

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A4')->getFont()->setSize(12);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A9')->getFont()->setBold(true);

        // Style the data headers (row 11)
        $headerRange = 'A11:L11';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A11:L'.(11 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align status columns
        $sheet->getStyle('E12:E'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L12:L'.(11 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align numeric columns
        $numericColumns = ['F', 'G', 'H', 'I', 'J'];
        foreach ($numericColumns as $column) {
            $sheet->getStyle($column.'12:'.$column.(11 + $this->collection()->count()))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Auto-size columns
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Truck Utilization';
    }
}
