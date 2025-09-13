<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\MonthlySalesReport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ExportMonthlySalesPdf
{
    public function execute(array $filters = []): Response
    {
        $report = app(MonthlySalesReport::class);
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $html = $this->generateHtml($data, $summary, $filters);

        $dompdf = new Dompdf;
        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultMediaType', 'screen');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('isJavascriptEnabled', false);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'monthly-sales-report-'.now()->format('Y-m-d-H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function generateHtml($data, $summary, array $filters): string
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monthly Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1f2937; margin: 0; font-size: 24px; }
        .header h2 { color: #6b7280; margin: 5px 0; font-size: 16px; font-weight: normal; }
        .summary { margin-bottom: 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        .summary-card { background: #f9fafb; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; text-align: center; }
        .summary-card h3 { margin: 0 0 5px 0; color: #374151; font-size: 14px; }
        .summary-card .value { font-size: 18px; font-weight: bold; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; color: #374151; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .revenue { color: #059669; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #6b7280; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Sales Report</h1>
        <h2>Period: '.$this->escapeHtml($startDate).' to '.$this->escapeHtml($endDate).'</h2>
        <h2>Generated: '.$this->escapeHtml(now()->format('M d, Y H:i:s')).'</h2>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Months</h3>
                <div class="value">'.number_format($summary['total_months']).'</div>
            </div>
            <div class="summary-card">
                <h3>Total Transactions</h3>
                <div class="value">'.number_format($summary['total_transactions']).'</div>
            </div>
            <div class="summary-card">
                <h3>Total ATC Cost</h3>
                <div class="value">₦'.number_format($summary['total_atc_cost'], 2).'</div>
            </div>
            <div class="summary-card">
                <h3>Total Transport Fees</h3>
                <div class="value">₦'.number_format($summary['total_transport_fees'], 2).'</div>
            </div>
        </div>
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <div class="value revenue">₦'.number_format($summary['total_revenue'], 2).'</div>
            </div>
            <div class="summary-card">
                <h3>Average Monthly Revenue</h3>
                <div class="value">₦'.number_format($summary['average_monthly_revenue'], 2).'</div>
            </div>
            <div class="summary-card">
                <h3>Best Month</h3>
                <div class="value">'.($summary['best_month'] ? $this->escapeHtml($summary['best_month']['month_name']) : 'N/A').'</div>
            </div>
            <div class="summary-card">
                <h3>Worst Month</h3>
                <div class="value">'.($summary['worst_month'] ? $this->escapeHtml($summary['worst_month']['month_name']) : 'N/A').'</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-right">Total Transactions</th>
                <th class="text-right">Total ATC Cost (₦)</th>
                <th class="text-right">Total Transport Fees (₦)</th>
                <th class="text-right">Total Revenue (₦)</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($data as $month) {
            $html .= '
            <tr>
                <td>'.$this->escapeHtml($month['month_name']).'</td>
                <td class="text-right">'.number_format($month['total_transactions']).'</td>
                <td class="text-right">₦'.number_format($month['total_atc_cost'], 2).'</td>
                <td class="text-right">₦'.number_format($month['total_transport_fees'], 2).'</td>
                <td class="text-right revenue">₦'.number_format($month['total_revenue'], 2).'</td>
            </tr>';
        }

        $html .= '
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated by RBC Trucking Management System</p>
        <p>For questions or support, please contact your system administrator</p>
    </div>
</body>
</html>';

        return $html;
    }

    private function escapeHtml($text): string
    {
        // Convert to string if not already
        if (! is_string($text)) {
            $text = (string) $text;
        }

        // Simple HTML escaping without complex UTF-8 handling
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
