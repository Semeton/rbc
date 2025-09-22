<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\PendingAtcReport;
use Carbon\Carbon;
// use Dompdf\Dompdf;
// use Dompdf\Options;
use Spatie\LaravelPdf\Facades\Pdf;
class ExportPendingAtcPdf
{
    public function execute(array $filters = []): mixed
    {
        $report = new PendingAtcReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $html = $this->generateHtml($data, $summary, $filters);

        $filename = 'pending-atc-report-'.now()->format('Y-m-d_H-i-s').'.pdf';

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
        $atcTypeFilter = $filters['atc_type'] ?? 'All Types';
        $statusFilter = isset($filters['status']) ? ($filters['status'] ? 'Active' : 'Inactive') : 'All Statuses';
        $companyFilter = $filters['company'] ?? 'All Companies';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Pending ATC Report</title>
            <style>
                body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary { margin-bottom: 30px; }
                .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; text-align: center; min-width: 150px; }
                .summary-value { font-size: 24px; font-weight: bold; color: #ea580c; }
                .summary-label { font-size: 12px; color: #666; margin-top: 5px; }
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
                <h1>Pending ATC Report</h1>
                <p>Generated on: '.now()->format('F d, Y').'</p>
                <p>ATC Type Filter: '.htmlspecialchars($atcTypeFilter, ENT_QUOTES, 'UTF-8').'</p>
                <p>Status Filter: '.htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8').'</p>
                <p>Company Filter: '.htmlspecialchars($companyFilter, ENT_QUOTES, 'UTF-8').'</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_atcs']).'</div>
                    <div class="summary-label">Total Pending ATCs</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">N'.number_format($summary['total_value'], 2).'</div>
                    <div class="summary-label">Total Value</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_tons']).'</div>
                    <div class="summary-label">Total Tons</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['active_atcs']).'</div>
                    <div class="summary-label">Active ATCs</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ATC Number</th>
                        <th>ATC Type</th>
                        <th>Company</th>
                        <th class="text-right">Amount (N)</th>
                        <th class="text-right">Tons</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Utilization</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $atc) {
            $html .= '
                    <tr>
                        <td>'.htmlspecialchars(strval($atc['atc_number']), ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.htmlspecialchars($atc['atc_type_display'], ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.htmlspecialchars($atc['company'], ENT_QUOTES, 'UTF-8').'</td>
                        <td class="text-right">'.number_format($atc['amount'], 2).'</td>
                        <td class="text-right">'.number_format($atc['tons']).'</td>
                        <td class="text-center">'.htmlspecialchars($atc['status_display'], ENT_QUOTES, 'UTF-8').'</td>
                        <td class="text-center">'.htmlspecialchars($atc['utilization_status'], ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.Carbon::parse($atc['created_at'])->format('M d, Y').'</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                <p>This report was generated by RBC Trucking Management System</p>
                <p>Average Value per ATC: N'.number_format($summary['average_value'], 2).'</p>
                <p>Average Tons per ATC: '.number_format($summary['average_tons'], 2).'</p>
                <p>Inactive ATCs: '.number_format($summary['inactive_atcs']).'</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
