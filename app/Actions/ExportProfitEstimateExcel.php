<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\ProfitEstimateReport;
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

class ExportProfitEstimateExcel
{
    public function execute(array $filters = []): BinaryFileResponse
    {
        $filename = 'profit-estimate-report-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new ProfitEstimateExcelExport($filters), $filename);
    }

    public static function export(array $filters = []): BinaryFileResponse
    {
        $filename = 'profit-estimate-report-'.now()->format('Y-m-d_H-i-s').'.xlsx';

        return Excel::download(new ProfitEstimateExcelExport($filters), $filename);
    }
}

class ProfitEstimateExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    private ProfitEstimateReport $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new ProfitEstimateReport;
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
            'Revenue (₦)',
            'Costs (₦)',
            'Profit (₦)',
            'Margin %',
        ];
    }

    public function map($record): array
    {
        $profitPrefix = $record['profit'] >= 0 ? '+' : '';
        
        return [
            Carbon::parse($record['date'])->format('M d, Y'),
            $record['day_of_week'],
            $record['total_revenue'],
            $record['total_costs'],
            $profitPrefix . $record['profit'],
            $record['profit_margin'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $summary = $this->report->getSummary($this->filters);
        $startDate = $this->filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');

        // Add summary information at the top
        $sheet->insertNewRowBefore(1, 15);

        $sheet->setCellValue('A1', 'Profit Estimate Report');
        $sheet->setCellValue('A2', 'Generated on: '.now()->format('F d, Y \a\t g:i A'));
        $sheet->setCellValue('A3', 'Period: '.Carbon::parse($startDate)->format('M d, Y').' - '.Carbon::parse($endDate)->format('M d, Y'));
        $sheet->setCellValue('A4', 'Formula: (ATC Cost + Transport Fee) – (Gas & Chop + Maintenance + Fare)');
        $sheet->setCellValue('A5', '');
        $sheet->setCellValue('A6', 'Summary:');
        $sheet->setCellValue('A7', 'Total Revenue: ₦'.number_format($summary['total_revenue'], 2).' | Total Costs: ₦'.number_format($summary['total_costs'], 2).' | Total Profit: ₦'.number_format($summary['total_profit'], 2));
        $sheet->setCellValue('A8', 'Profit Margin: '.number_format($summary['overall_profit_margin'], 2).'% | Active Days: '.number_format($summary['active_days']).' / '.number_format($summary['total_days']));
        $sheet->setCellValue('A9', 'Average Profit per Day: ₦'.number_format($summary['average_profit_per_day'], 2).' | Average Revenue per Day: ₦'.number_format($summary['average_revenue_per_day'], 2));
        $sheet->setCellValue('A10', 'Most Profitable Day: '.($summary['most_profitable_day'] ? $summary['most_profitable_day']['date'].' (₦'.number_format($summary['most_profitable_day']['profit'], 2).')' : 'N/A'));
        $sheet->setCellValue('A11', '');
        $sheet->setCellValue('A12', 'Revenue Breakdown:');
        $sheet->setCellValue('A13', 'ATC Cost: ₦'.number_format($summary['total_atc_cost'], 2).' | Transport Fee: ₦'.number_format($summary['total_transport_fee'], 2));
        $sheet->setCellValue('A14', 'Cost Breakdown:');
        $sheet->setCellValue('A15', 'Gas & Chop: ₦'.number_format($summary['total_gas_chop'], 2).' | Fare: ₦'.number_format($summary['total_fare'], 2).' | Maintenance: ₦'.number_format($summary['total_maintenance'], 2));

        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A4')->getFont()->setSize(12);
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A10')->getFont()->setBold(true);
        $sheet->getStyle('A12')->getFont()->setBold(true);
        $sheet->getStyle('A13')->getFont()->setBold(true);
        $sheet->getStyle('A14')->getFont()->setBold(true);
        $sheet->getStyle('A15')->getFont()->setBold(true);

        // Style the data headers (row 17)
        $headerRange = 'A17:F17';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Style all data cells
        $dataRange = 'A17:F'.(17 + $this->collection()->count());
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align day and margin columns
        $sheet->getStyle('B18:B'.(17 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F18:F'.(17 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Right align currency columns
        $sheet->getStyle('C18:E'.(17 + $this->collection()->count()))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Profit Estimate';
    }
}
