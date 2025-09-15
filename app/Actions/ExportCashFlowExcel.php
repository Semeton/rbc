<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\CashFlowReport;
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

class ExportCashFlowExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'cash-flow-report-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new CashFlowExcelExport($filters), $filename);
    }

    public static function export(array $filters = []): BinaryFileResponse
    {
        $filename = 'cash-flow-report-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new CashFlowExcelExport($filters), $filename);
    }
}

class CashFlowExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private CashFlowReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new CashFlowReport;
    }

    public function collection()
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Category',
            'Description',
            'Amount (₦)',
        ];
    }

    public function map($record): array
    {
        $amountPrefix = $record['type'] === 'incoming' ? '+' : '-';

        return [
            Carbon::parse($record['date'])->format('M d, Y'),
            ucfirst($record['type']),
            $record['category'],
            $record['description'],
            $amountPrefix.$record['amount'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 12);

        $sheet->setCellValue('A1', 'Cash Flow Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.Carbon::parse($startDate)->format('M d, Y').' - '.Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', '');
        $sheet->setCellValue('A5', 'Summary:');
        $sheet->setCellValue('A6', 'Total Incoming: ₦'.number_format($summary['total_incoming'], 2).' | Total Outgoing: ₦'.number_format($summary['total_outgoing'], 2));
        $sheet->setCellValue('A7', 'Net Cash Flow: ₦'.number_format($summary['net_cash_flow'], 2).' | Total Transactions: '.number_format($summary['incoming_count'] + $summary['outgoing_count']));
        $sheet->setCellValue('A8', 'Maintenance: ₦'.number_format($summary['total_maintenance'], 2).' | Gas & Chop: ₦'.number_format($summary['total_gas_chop'], 2).' | Fare: ₦'.number_format($summary['total_fare'], 2));
        $sheet->setCellValue('A9', 'Incoming Percentage: '.number_format($summary['incoming_percentage'], 1).'% | Outgoing Percentage: '.number_format($summary['outgoing_percentage'], 1).'%');
        $sheet->setCellValue('A10', 'Cash Flow Status: '.($summary['net_cash_flow'] >= 0 ? 'Positive' : 'Negative'));

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A3')->getFont()->setSize(12);
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A6:A10')->getFont()->setBold(true);

        // Style the data headers (row 13)
        $headerRange = 'A13:E13';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A13:E'.(13 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align type column
        $sheet->getStyle('B14:B'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align amount column
        $sheet->getStyle('E14:E'.(13 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto-size columns
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Cash Flow Report';
    }
}
