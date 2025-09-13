<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\CustomerBalanceReport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ExportCustomerBalancePdf
{
    public function execute(array $filters = []): Response
    {
        $report = app(CustomerBalanceReport::class);
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

        $filename = 'customer-balance-report-'.now()->format('Y-m-d-H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function generateHtml($data, $summary, array $filters): string
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer Balance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1f2937; margin: 0; font-size: 24px; }
        .header h2 { color: #6b7280; margin: 5px 0; font-size: 16px; font-weight: normal; }
        .summary { margin-bottom: 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
        .summary-card { background: #f9fafb; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; text-align: center; }
        .summary-card h3 { margin: 0 0 5px 0; color: #374151; font-size: 14px; }
        .summary-card .value { font-size: 18px; font-weight: bold; color: #1f2937; }
        .summary-card .value.positive { color: #059669; }
        .summary-card .value.negative { color: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; color: #374151; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .positive { color: #059669; font-weight: bold; }
        .negative { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #6b7280; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Balance Report</h1>
        <h2>Period: '.$this->escapeHtml($startDate).' to '.$this->escapeHtml($endDate).'</h2>
        <h2>Generated: '.$this->escapeHtml(now()->format('M d, Y H:i:s')).'</h2>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Customers</h3>
                <div class="value">'.number_format($summary['total_customers']).'</div>
            </div>
            <div class="summary-card">
                <h3>Total ATC Value</h3>
                <div class="value">₦'.number_format($summary['total_atc_value'], 2).'</div>
            </div>
            <div class="summary-card">
                <h3>Total Payments</h3>
                <div class="value">₦'.number_format($summary['total_payments'], 2).'</div>
            </div>
        </div>
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Outstanding Balance</h3>
                <div class="value '.($summary['total_outstanding_balance'] > 0 ? 'negative' : 'positive').'">₦'.number_format($summary['total_outstanding_balance'], 2).'</div>
            </div>
            <div class="summary-card">
                <h3>Customers with Debt</h3>
                <div class="value">'.number_format($summary['customers_with_debt']).'</div>
            </div>
            <div class="summary-card">
                <h3>Customers with Credit</h3>
                <div class="value">'.number_format($summary['customers_with_credit']).'</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Customer Name</th>
                <th class="text-right">Total ATC Value (₦)</th>
                <th class="text-right">Total Payments (₦)</th>
                <th class="text-right">Outstanding Balance (₦)</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($data as $customer) {
            $balanceClass = $customer['outstanding_balance'] > 0 ? 'negative' : ($customer['outstanding_balance'] < 0 ? 'positive' : '');

            $html .= '
            <tr>
                <td>'.$this->escapeHtml($customer['customer_name']).'</td>
                <td class="text-right">₦'.number_format($customer['total_atc_value'], 2).'</td>
                <td class="text-right">₦'.number_format($customer['total_payments'], 2).'</td>
                <td class="text-right '.$balanceClass.'">₦'.number_format($customer['outstanding_balance'], 2).'</td>
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
