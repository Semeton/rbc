<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\MonthlySalesReport;
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

class ExportMonthlySalesExcel implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private array $summary;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $report = app(MonthlySalesReport::class);
        $this->summary = $report->getSummary($filters);
    }

    public function collection(): Collection
    {
        $report = app(MonthlySalesReport::class);

        return $report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Month',
            'Total Transactions',
            'Total ATC Cost (₦)',
            'Total Transport Fees (₦)',
            'Total Revenue (₦)',
        ];
    }

    public function map($month): array
    {
        return [
            $month['month_name'],
            $month['total_transactions'],
            $month['total_atc_cost'],
            $month['total_transport_fees'],
            $month['total_revenue'],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(18);

        // Style header row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1f2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Add summary information
        $startDate = $this->filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');

        $sheet->insertNewRowBefore(1, 10);
        $sheet->setCellValue('A1', 'Monthly Sales Report');
        $sheet->setCellValue('A2', 'Period: '.$startDate.' to '.$endDate);
        $sheet->setCellValue('A3', 'Generated: '.now()->format('M d, Y H:i:s'));
        $sheet->setCellValue('A4', 'Total Months: '.number_format($this->summary['total_months']));
        $sheet->setCellValue('A5', 'Total Transactions: '.number_format($this->summary['total_transactions']));
        $sheet->setCellValue('A6', 'Total ATC Cost: ₦'.number_format($this->summary['total_atc_cost'], 2));
        $sheet->setCellValue('A7', 'Total Transport Fees: ₦'.number_format($this->summary['total_transport_fees'], 2));
        $sheet->setCellValue('A8', 'Total Revenue: ₦'.number_format($this->summary['total_revenue'], 2));
        $sheet->setCellValue('A9', 'Average Monthly Revenue: ₦'.number_format($this->summary['average_monthly_revenue'], 2));
        $sheet->setCellValue('A10', 'Best Month: '.($this->summary['best_month'] ? $this->summary['best_month']['month_name'] : 'N/A'));

        // Style summary section
        $sheet->getStyle('A1:A10')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Style data rows
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A11:E'.$lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'd1d5db'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Style numeric columns (right align)
        $sheet->getStyle('B11:E'.$lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Auto-filter
        $sheet->setAutoFilter('A11:E'.$lastRow);
    }

    public function title(): string
    {
        return 'Monthly Sales';
    }

    public static function export(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'monthly-sales-report-'.now()->format('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new self($filters), $filename);
    }
}
