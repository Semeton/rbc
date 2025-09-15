<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\ProfitEstimateReport;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ExportProfitEstimatePdf
{
    public function execute(array $filters = []): Response
    {
        $report = new ProfitEstimateReport;
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

        $filename = 'profit-estimate-report-'.now()->format('Y-m-d_H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function generateHtml($data, $summary, $filters): string
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Profit Estimate Report</title>
            <style>
                body { font-family: DejaVu Sans, Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary { margin-bottom: 30px; }
                .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; text-align: center; min-width: 150px; }
                .summary-value { font-size: 24px; font-weight: bold; }
                .summary-label { font-size: 12px; color: #666; margin-top: 5px; }
                .positive { color: #16a34a; }
                .negative { color: #dc2626; }
                .breakdown { margin-bottom: 30px; }
                .breakdown-section { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; min-width: 200px; vertical-align: top; }
                .breakdown-title { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
                .breakdown-item { display: flex; justify-content: space-between; margin-bottom: 5px; }
                .breakdown-total { border-top: 1px solid #ddd; padding-top: 5px; margin-top: 10px; font-weight: bold; }
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
                <h1>Profit Estimate Report</h1>
                <p>Generated on: '.now()->format('F d, Y').'</p>
                <p>Period: '.Carbon::parse($startDate)->format('M d, Y').' - '.Carbon::parse($endDate)->format('M d, Y').'</p>
                <p>Formula: (ATC Cost + Transport Fee) – (Gas & Chop + Maintenance + Fare)</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value positive">N'.number_format($summary['total_revenue'], 2).'</div>
                    <div class="summary-label">Total Revenue</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value negative">N'.number_format($summary['total_costs'], 2).'</div>
                    <div class="summary-label">Total Costs</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value '.($summary['total_profit'] >= 0 ? 'positive' : 'negative').'">N'.number_format($summary['total_profit'], 2).'</div>
                    <div class="summary-label">Total Profit</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value '.($summary['overall_profit_margin'] >= 0 ? 'positive' : 'negative').'">'.number_format($summary['overall_profit_margin'], 2).'%</div>
                    <div class="summary-label">Profit Margin</div>
                </div>
            </div>

            <div class="breakdown">
                <div class="breakdown-section">
                    <div class="breakdown-title">Revenue Breakdown</div>
                    <div class="breakdown-item">
                        <span>ATC Cost:</span>
                        <span>N'.number_format($summary['total_atc_cost'], 2).'</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Transport Fee:</span>
                        <span>N'.number_format($summary['total_transport_fee'], 2).'</span>
                    </div>
                    <div class="breakdown-total">
                        <div class="breakdown-item">
                            <span>Total Revenue:</span>
                            <span class="positive">N'.number_format($summary['total_revenue'], 2).'</span>
                        </div>
                    </div>
                </div>

                <div class="breakdown-section">
                    <div class="breakdown-title">Cost Breakdown</div>
                    <div class="breakdown-item">
                        <span>Gas & Chop:</span>
                        <span>N'.number_format($summary['total_gas_chop'], 2).'</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Fare:</span>
                        <span>N'.number_format($summary['total_fare'], 2).'</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Maintenance:</span>
                        <span>N'.number_format($summary['total_maintenance'], 2).'</span>
                    </div>
                    <div class="breakdown-total">
                        <div class="breakdown-item">
                            <span>Total Costs:</span>
                            <span class="negative">N'.number_format($summary['total_costs'], 2).'</span>
                        </div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-center">Day</th>
                        <th class="text-right">Revenue (N)</th>
                        <th class="text-right">Costs (N)</th>
                        <th class="text-right">Profit (N)</th>
                        <th class="text-center">Margin %</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $day) {
            $rowClass = $day['has_activity'] ? '' : 'inactive';
            $profitClass = $day['profit'] >= 0 ? 'positive' : 'negative';
            $marginClass = $day['profit_margin'] >= 0 ? 'positive' : 'negative';
            $profitPrefix = $day['profit'] >= 0 ? '+' : '';
            
            $html .= '
                    <tr class="'.$rowClass.'">
                        <td>'.Carbon::parse($day['date'])->format('M d, Y').'</td>
                        <td class="text-center">'.$day['day_of_week'].'</td>
                        <td class="text-right">'.number_format($day['total_revenue'], 2).'</td>
                        <td class="text-right">'.number_format($day['total_costs'], 2).'</td>
                        <td class="text-right '.$profitClass.'">'.$profitPrefix.number_format($day['profit'], 2).'</td>
                        <td class="text-center '.$marginClass.'">'.number_format($day['profit_margin'], 2).'%</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                <p>This report was generated by RBC Trucking Management System</p>
                <p>Average Profit per Day: N'.number_format($summary['average_profit_per_day'], 2).'</p>
                <p>Most Profitable Day: '.($summary['most_profitable_day'] ? $summary['most_profitable_day']['date'].' (N'.number_format($summary['most_profitable_day']['profit'], 2).')' : 'N/A').'</p>
                <p>Active Days: '.number_format($summary['active_days']).' / '.number_format($summary['total_days']).'</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
