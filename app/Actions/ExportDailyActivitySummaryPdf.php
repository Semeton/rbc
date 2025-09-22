<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\DailyActivitySummaryReport;
use Carbon\Carbon;
// use Dompdf\Dompdf;
// use Dompdf\Options;
use Spatie\LaravelPdf\Facades\Pdf;
class ExportDailyActivitySummaryPdf
{
    public function execute(array $filters = []): mixed
    {
        $report = new DailyActivitySummaryReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $html = $this->generateHtml($data, $summary, $filters);

        $filename = 'daily-activity-summary-'.now()->format('Y-m-d_H-i-s').'.pdf';

        return response()->streamDownload(function () use ($html, $filename) {
            echo Pdf::html($html)
                ->format('a4')
                ->landscape()
                ->name($filename)
                ->toResponse(request());
        }, $filename);
    }

    private function generateHtml($data, $summary, $filters): string
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daily Activity Summary Report</title>
            <style>
                body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary { margin-bottom: 30px; }
                .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; text-align: center; min-width: 150px; }
                .summary-value { font-size: 24px; font-weight: bold; }
                .summary-label { font-size: 12px; color: #666; margin-top: 5px; }
                .positive { color: #16a34a; }
                .negative { color: #dc2626; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
                .inactive { opacity: 0.5; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Daily Activity Summary Report</h1>
                <p>Generated on: '.now()->format('F d, Y').'</p>
                <p>Period: '.Carbon::parse($startDate)->format('M d, Y').' - '.Carbon::parse($endDate)->format('M d, Y').'</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_transactions']).'</div>
                    <div class="summary-label">Total Transactions</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">N'.number_format($summary['total_sales'], 2).'</div>
                    <div class="summary-label">Total Sales</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">N'.number_format($summary['total_payments_amount'], 2).'</div>
                    <div class="summary-label">Total Payments</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value '.($summary['net_activity'] >= 0 ? 'positive' : 'negative').'">N'.number_format($summary['net_activity'], 2).'</div>
                    <div class="summary-label">Net Activity</div>
                </div>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['active_days']).' / '.number_format($summary['total_days']).'</div>
                    <div class="summary-label">Active Days</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['average_transactions_per_day'], 1).'</div>
                    <div class="summary-label">Avg Transactions/Day</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.($summary['busiest_day'] ? $summary['busiest_day']['date'] : 'N/A').'</div>
                    <div class="summary-label">Busiest Day</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-center">Day</th>
                        <th class="text-center">Transactions</th>
                        <th class="text-center">Payments</th>
                        <th class="text-right">Total Sales (N)</th>
                        <th class="text-right">Total Payments (N)</th>
                        <th class="text-right">Net Activity (N)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $day) {
            $rowClass = $day['has_activity'] ? '' : 'inactive';
            $netClass = $day['net_activity'] >= 0 ? 'positive' : 'negative';
            $netPrefix = $day['net_activity'] >= 0 ? '+' : '';

            $html .= '
                    <tr class="'.$rowClass.'">
                        <td>'.Carbon::parse($day['date'])->format('M d, Y').'</td>
                        <td class="text-center">'.$day['day_of_week'].'</td>
                        <td class="text-center">'.$day['transaction_count'].'</td>
                        <td class="text-center">'.$day['payment_count'].'</td>
                        <td class="text-right">'.number_format($day['total_sales'], 2).'</td>
                        <td class="text-right">'.number_format($day['total_payments'], 2).'</td>
                        <td class="text-right '.$netClass.'">'.$netPrefix.number_format($day['net_activity'], 2).'</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                <p>This report was generated by RBC Trucking Management System</p>
                <p>Average Sales per Day: N'.number_format($summary['average_sales_per_day'], 2).'</p>
                <p>Average Payments per Day: N'.number_format($summary['average_payments_per_day'], 2).'</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
