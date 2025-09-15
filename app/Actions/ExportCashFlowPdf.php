<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\CashFlowReport;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ExportCashFlowPdf
{
    public function execute(array $filters = []): Response
    {
        $report = new CashFlowReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $html = $this->generateHtml($data, $summary, $filters);

        $dompdf = new Dompdf;
        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('isJavascriptEnabled', false);
        $options->set('defaultMediaType', 'screen');
        $options->set('isFontSubsettingEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'cash-flow-report-'.now()->format('Y-m-d_H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
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
            <title>Cash Flow Report</title>
            <style>
                body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary { margin-bottom: 30px; }
                .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; text-align: center; min-width: 150px; }
                .summary-value { font-size: 24px; font-weight: bold; }
                .summary-label { font-size: 12px; color: #666; margin-top: 5px; }
                .incoming { color: #16a34a; }
                .outgoing { color: #dc2626; }
                .net-positive { color: #16a34a; }
                .net-negative { color: #dc2626; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Cash Flow Report</h1>
                <p>Generated on: '.now()->format('F d, Y').'</p>
                <p>Period: '.Carbon::parse($startDate)->format('M d, Y').' - '.Carbon::parse($endDate)->format('M d, Y').'</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value incoming">N'.number_format($summary['total_incoming'], 2).'</div>
                    <div class="summary-label">Total Incoming</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value outgoing">N'.number_format($summary['total_outgoing'], 2).'</div>
                    <div class="summary-label">Total Outgoing</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value '.($summary['net_cash_flow'] >= 0 ? 'net-positive' : 'net-negative').'">N'.number_format($summary['net_cash_flow'], 2).'</div>
                    <div class="summary-label">Net Cash Flow</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['incoming_count'] + $summary['outgoing_count']).'</div>
                    <div class="summary-label">Total Transactions</div>
                </div>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value outgoing">N'.number_format($summary['total_maintenance'], 2).'</div>
                    <div class="summary-label">Maintenance</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value outgoing">N'.number_format($summary['total_gas_chop'], 2).'</div>
                    <div class="summary-label">Gas & Chop</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value outgoing">N'.number_format($summary['total_fare'], 2).'</div>
                    <div class="summary-label">Fare</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-center">Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th class="text-right">Amount (N)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $transaction) {
            $amountClass = $transaction['type'] === 'incoming' ? 'incoming' : 'outgoing';
            $amountPrefix = $transaction['type'] === 'incoming' ? '+' : '-';

            $html .= '
                    <tr>
                        <td>'.Carbon::parse($transaction['date'])->format('M d, Y').'</td>
                        <td class="text-center">'.ucfirst($transaction['type']).'</td>
                        <td>'.htmlspecialchars($transaction['category'], ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.htmlspecialchars($transaction['description'], ENT_QUOTES, 'UTF-8').'</td>
                        <td class="text-right '.$amountClass.'">'.$amountPrefix.'N'.number_format((float) $transaction['amount'], 2).'</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                <p>This report was generated by RBC Trucking Management System</p>
                <p>Incoming Percentage: '.number_format($summary['incoming_percentage'], 1).'% | Outgoing Percentage: '.number_format($summary['outgoing_percentage'], 1).'%</p>
                <p>Cash Flow Status: '.($summary['net_cash_flow'] >= 0 ? 'Positive' : 'Negative').'</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
