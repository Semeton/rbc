<?php

namespace App\Actions;

use App\Reports\CustomerPaymentHistoryReport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ExportCustomerPaymentHistoryPdf
{
    public function execute(array $filters = []): Response
    {
        $report = new CustomerPaymentHistoryReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $html = $this->generateHtml($data, $summary, $filters);

        $options = new Options;
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'customer_payment_history_'.now()->format('Y-m-d_H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function generateHtml($data, $summary, $filters): string
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $customerName = $filters['customer_id'] ? \App\Models\Customer::find($filters['customer_id'])->name : 'All Customers';
        $paymentType = $filters['payment_type'] ?? 'All Types';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Customer Payment History Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary { margin-bottom: 30px; }
                .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; text-align: center; min-width: 150px; }
                .summary-value { font-size: 24px; font-weight: bold; color: #2563eb; }
                .summary-label { font-size: 12px; color: #666; margin-top: 5px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
                .badge-cash { background-color: #fef3c7; color: #92400e; }
                .badge-transfer { background-color: #dbeafe; color: #1e40af; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Customer Payment History Report</h1>
                <p>Generated on: '.now()->format('F d, Y \a\t g:i A').'</p>
                <p>Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y').'</p>
                <p>Customer: '.htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8').' | Payment Type: '.htmlspecialchars($paymentType, ENT_QUOTES, 'UTF-8').'</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['total_payments'], 2).'</div>
                    <div class="summary-label">Total Payments</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_transactions']).'</div>
                    <div class="summary-label">Total Transactions</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['cash_payments'], 2).'</div>
                    <div class="summary-label">Cash Payments</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['transfer_payments'], 2).'</div>
                    <div class="summary-label">Transfer Payments</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['average_payment'], 2).'</div>
                    <div class="summary-label">Average Payment</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Customer Name</th>
                        <th class="text-right">Amount Paid (₦)</th>
                        <th class="text-center">Payment Type</th>
                        <th>Bank Name</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $payment) {
            $badgeClass = $payment['payment_type'] === 'Cash' ? 'badge-cash' : 'badge-transfer';
            $html .= '
                    <tr>
                        <td>'.\Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y').'</td>
                        <td>'.htmlspecialchars($payment['customer_name'], ENT_QUOTES, 'UTF-8').'</td>
                        <td class="text-right">'.number_format($payment['amount_paid'], 2).'</td>
                        <td class="text-center"><span class="badge '.$badgeClass.'">'.htmlspecialchars($payment['payment_type'], ENT_QUOTES, 'UTF-8').'</span></td>
                        <td>'.htmlspecialchars($payment['bank_name'], ENT_QUOTES, 'UTF-8').'</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                <p>This report was generated by RBC Trucking Management System</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
