<?php

namespace App\Actions;

use App\Reports\TruckUtilizationReport;
// use Dompdf\Dompdf;
// use Dompdf\Options;
use Spatie\LaravelPdf\Facades\Pdf;
class ExportTruckUtilizationPdf
{
    public function execute(array $filters = []): mixed
    {
        $report = new TruckUtilizationReport;
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        $html = $this->generateHtml($data, $summary, $filters);

        $filename = 'truck_utilization_'.now()->format('Y-m-d_H-i-s').'.pdf';

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
        $startDate = $filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $truckName = $filters['truck_id'] ? \App\Models\Truck::find($filters['truck_id'])->cab_number : 'All Trucks';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Truck Utilization Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary { margin-bottom: 30px; }
                .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; text-align: center; min-width: 150px; }
                .summary-value { font-size: 24px; font-weight: bold; color: #ea580c; }
                .summary-label { font-size: 12px; color: #666; margin-top: 5px; }
                .notice { background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
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
                <h1>Truck Utilization Report</h1>
                <p>Generated on: '.now()->format('F d, Y \a\t g:i A').'</p>
                <p>Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y').'</p>
                <p>Truck Filter: '.htmlspecialchars($truckName, ENT_QUOTES, 'UTF-8').'</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_trucks']).'</div>
                    <div class="summary-label">Total Trucks</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['active_trucks']).'</div>
                    <div class="summary-label">Active Trucks</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_trips']).'</div>
                    <div class="summary-label">Total Trips</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['total_income_generated'], 2).'</div>
                    <div class="summary-label">Total Fare Generated</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['total_gas_chop_money'], 2).'</div>
                    <div class="summary-label">Total Gas & Chop Money</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['total_balance'], 2).'</div>
                    <div class="summary-label">Total Balance</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['total_maintenance_cost'], 2).'</div>
                    <div class="summary-label">Total Maintenance Cost</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_maintenance_records']).'</div>
                    <div class="summary-label">Total Maintenance Records</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Cab Number</th>
                        <th>Registration Number</th>
                        <th>Truck Model</th>
                        <th>Year</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Total Trips</th>
                        <th class="text-right">Total Fare (₦)</th>
                        <th class="text-right">Gas & Chop Money (₦)</th>
                        <th class="text-right">Balance (₦)</th>
                        <th class="text-right">Maintenance Cost (₦)</th>
                        <th class="text-right">Maintenance Records</th>
                        <th class="text-center">Utilization Status</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $truck) {
            $html .= '
                    <tr>
                        <td>'.htmlspecialchars($truck['cab_number'], ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.htmlspecialchars($truck['registration_number'], ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.htmlspecialchars($truck['truck_model'], ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.$truck['year_of_manufacture'].'</td>
                        <td class="text-center">'.($truck['truck_status'] ? 'Active' : 'Inactive').'</td>
                        <td class="text-right">'.number_format($truck['total_trips']).'</td>
                        <td class="text-right">'.number_format($truck['total_income_generated'], 2).'</td>
                        <td class="text-right">'.number_format($truck['total_gas_chop_money'], 2).'</td>
                        <td class="text-right">'.number_format($truck['total_balance'], 2).'</td>
                        <td class="text-right">'.number_format($truck['total_maintenance_cost'], 2).'</td>
                        <td class="text-right">'.number_format($truck['total_maintenance_records']).'</td>
                        <td class="text-center">'.htmlspecialchars($truck['utilization_status'], ENT_QUOTES, 'UTF-8').'</td>
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
