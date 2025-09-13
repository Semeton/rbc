<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\OutstandingBalancesReport;
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

class ExportOutstandingBalancesExcel implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private array $summary;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $report = app(OutstandingBalancesReport::class);
        $this->summary = $report->getSummary($filters);
    }

    public function collection(): Collection
    {
        $report = app(OutstandingBalancesReport::class);

        return $report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Last Payment Date',
            'Outstanding Amount',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer['customer_name'],
            $customer['last_payment_date'] ? (is_string($customer['last_payment_date']) ? date('Y-m-d', strtotime($customer['last_payment_date'])) : $customer['last_payment_date']->format('Y-m-d')) : 'Never',
            $customer['outstanding_amount'],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(18);

        // Style header row
        $sheet->getStyle('A1:C1')->applyFromArray([
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

        $sheet->insertNewRowBefore(1, 5);
        $sheet->setCellValue('A1', 'Outstanding Balances Report');
        $sheet->setCellValue('A2', 'Period: '.$startDate.' to '.$endDate);
        $sheet->setCellValue('A3', 'Generated: '.now()->format('M d, Y H:i:s'));
        $sheet->setCellValue('A4', 'Customers with Debt: '.number_format($this->summary['total_customers_with_debt']));
        $sheet->setCellValue('A5', 'Total Outstanding Amount: $'.number_format($this->summary['total_outstanding_amount'], 2));

        // Style summary section
        $sheet->getStyle('A1:A5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Style data rows
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A6:C'.$lastRow)->applyFromArray([
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
        $sheet->getStyle('C6:C'.$lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'font' => [
                'color' => ['rgb' => 'dc2626'],
                'bold' => true,
            ],
        ]);

        // Auto-filter
        $sheet->setAutoFilter('A6:C'.$lastRow);
    }

    public function title(): string
    {
        return 'Outstanding Balances';
    }

    public static function export(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'outstanding-balances-report-'.now()->format('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new self($filters), $filename);
    }
}
