<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\DailyActivitySummaryReport;
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

class ExportDailyActivitySummaryExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'daily-activity-summary-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new DailyActivitySummaryExcelExport($filters), $filename);
    }

    public static function export(array $filters = []): BinaryFileResponse
    {
        $filename = 'daily-activity-summary-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new DailyActivitySummaryExcelExport($filters), $filename);
    }
}

class DailyActivitySummaryExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private DailyActivitySummaryReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new DailyActivitySummaryReport;
    }

    public function collection()
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Day',
            'Transactions',
            'Payments',
            'Total Sales (₦)',
            'Total Payments (₦)',
            'Net Activity (₦)',
        ];
    }

    public function map($record): array
    {
        $netPrefix = $record['net_activity'] >= 0 ? '+' : '';

        return [
            Carbon::parse($record['date'])->format('M d, Y'),
            $record['day_of_week'],
            $record['transaction_count'],
            $record['payment_count'],
            $record['total_sales'],
            $record['total_payments'],
            $netPrefix.$record['net_activity'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 12);

        $sheet->setCellValue('A1', 'Daily Activity Summary Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.Carbon::parse($startDate)->format('M d, Y').' - '.Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', '');
        $sheet->setCellValue('A5', 'Summary:');
        $sheet->setCellValue('A6', 'Total Transactions: '.number_format($summary['total_transactions']).' | Total Sales: ₦'.number_format($summary['total_sales'], 2).' | Total Payments: ₦'.number_format($summary['total_payments_amount'], 2));
        $sheet->setCellValue('A7', 'Net Activity: ₦'.number_format($summary['net_activity'], 2).' | Active Days: '.number_format($summary['active_days']).' / '.number_format($summary['total_days']));
        $sheet->setCellValue('A8', 'Average Transactions per Day: '.number_format($summary['average_transactions_per_day'], 1).' | Average Sales per Day: ₦'.number_format($summary['average_sales_per_day'], 2));
        $sheet->setCellValue('A9', 'Average Payments per Day: ₦'.number_format($summary['average_payments_per_day'], 2).' | Busiest Day: '.($summary['busiest_day'] ? $summary['busiest_day']['date'] : 'N/A'));

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A3')->getFont()->setSize(12);
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A6:A9')->getFont()->setBold(true);

        // Style the data headers (row 13)
        $headerRange = 'A13:G13';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A13:G'.(13 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align day, transactions, and payments columns
        $sheet->getStyle('B14:B'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C14:C'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D14:D'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align currency columns
        $sheet->getStyle('E14:E'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F14:F'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G14:G'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto-size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Daily Activity Summary';
    }
}
