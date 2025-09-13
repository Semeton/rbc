<?php

namespace App\Actions;

use App\Reports\CustomerPaymentHistoryReport;
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

class ExportCustomerPaymentHistoryExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'customer_payment_history_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new CustomerPaymentHistoryExcelExport($filters), $filename);
    }
}

class CustomerPaymentHistoryExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private CustomerPaymentHistoryReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new CustomerPaymentHistoryReport;
    }

    public function collection()
    {
        return $this->report->generate($this->filters);
    }

    public function headings(): array
    {
        return [
            'Payment Date',
            'Customer Name',
            'Amount Paid (₦)',
            'Payment Type',
            'Bank Name',
        ];
    }

    public function map($payment): array
    {
        return [
            \Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y'),
            $payment['customer_name'],
            $payment['amount_paid'],
            $payment['payment_type'],
            $payment['bank_name'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $customerName = $this->filters['customer_id'] ? \App\Models\Customer::find($this->filters['customer_id'])->name : 'All Customers';
        $paymentType = $this->filters['payment_type'] ?? 'All Types';

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 8);

        $sheet->setCellValue('A1', 'Customer Payment History Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', 'Customer: '.$customerName.' | Payment Type: '.$paymentType);
        $sheet->setCellValue('A5', '');
        $sheet->setCellValue('A6', 'Summary:');
        $sheet->setCellValue('A7', 'Total Payments: ₦'.number_format($summary['total_payments'], 2));
        $sheet->setCellValue('A8', 'Total Transactions: '.number_format($summary['total_transactions']));

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

        // Right align amount column
        $sheet->getStyle('C10:C'.(9 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Center align payment type column
        $sheet->getStyle('D10:D'.(9 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-size columns
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Payment History';
    }
}
