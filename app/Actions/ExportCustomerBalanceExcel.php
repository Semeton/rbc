<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\CustomerBalanceReport;
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

class ExportCustomerBalanceExcel implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private array $summary;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $report = app(CustomerBalanceReport::class);
        $this->summary = $report->getSummary($filters);
    }

    public function collection(): Collection
    {
        $report = app(CustomerBalanceReport::class);

        return $report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Total ATC Value (₦)',
            'Total Payments (₦)',
            'Outstanding Balance (₦)',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer['customer_name'],
            $customer['total_atc_value'],
            $customer['total_payments'],
            $customer['outstanding_balance'],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(20);

        // Style header row
        $sheet->getStyle('A1:D1')->applyFromArray([
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
        $startDate = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        $sheet->insertNewRowBefore(1, 8);
        $sheet->setCellValue('A1', 'Customer Balance Report');
        $sheet->setCellValue('A2', 'Period: '.$startDate.' to '.$endDate);
        $sheet->setCellValue('A3', 'Generated: '.now()->format('M d, Y H:i:s'));
        $sheet->setCellValue('A4', 'Total Customers: '.number_format($this->summary['total_customers']));
        $sheet->setCellValue('A5', 'Total ATC Value: ₦'.number_format($this->summary['total_atc_value'], 2));
        $sheet->setCellValue('A6', 'Total Payments: ₦'.number_format($this->summary['total_payments'], 2));
        $sheet->setCellValue('A7', 'Outstanding Balance: ₦'.number_format($this->summary['total_outstanding_balance'], 2));
        $sheet->setCellValue('A8', 'Customers with Debt: '.number_format($this->summary['customers_with_debt']));

        // Style summary section
        $sheet->getStyle('A1:A8')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Style data rows
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A9:D'.$lastRow)->applyFromArray([
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
        $sheet->getStyle('B9:D'.$lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Auto-filter
        $sheet->setAutoFilter('A9:D'.$lastRow);
    }

    public function title(): string
    {
        return 'Customer Balance';
    }

    public static function export(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'customer-balance-report-'.now()->format('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new self($filters), $filename);
    }
}
