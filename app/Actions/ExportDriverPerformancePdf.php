<?php

namespace App\Actions;

use App\Reports\DriverPerformanceReport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ExportDriverPerformancePdf
{
    public function execute(array $filters = []): Response
    {
        $report = new DriverPerformanceReport;
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

        $filename = 'driver_performance_'.now()->format('Y-m-d_H-i-s').'.pdf';


        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function generateHtml($data, $summary, $filters): string
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');
        $driverName = $filters['driver_id'] ? \App\Models\Driver::find($filters['driver_id'])->name : 'All Drivers';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Driver Performance Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary { margin-bottom: 30px; }
                .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; text-align: center; min-width: 150px; }
                .summary-value { font-size: 24px; font-weight: bold; color: #059669; }
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
                <h1>Driver Performance Report</h1>
                <p>Generated on: '.now()->format('F d, Y \a\t g:i A').'</p>
                <p>Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y').'</p>
                <p>Driver Filter: '.htmlspecialchars($driverName, ENT_QUOTES, 'UTF-8').'</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_drivers']).'</div>
                    <div class="summary-label">Total Drivers</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_trips']).'</div>
                    <div class="summary-label">Total Trips</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">₦'.number_format($summary['total_fare_earned'], 2).'</div>
                    <div class="summary-label">Total Fare Earned</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.htmlspecialchars($summary['top_performer'], ENT_QUOTES, 'UTF-8').'</div>
                    <div class="summary-label">Top Performer</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Driver Name</th>
                        <th class="text-right">Number of Trips</th>
                        <th class="text-right">Total Fare Earned (₦)</th>
                        <th class="text-right">Avg Fare per Trip (₦)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $driver) {
            $avgFarePerTrip = $driver['number_of_trips'] > 0 ? $driver['total_fare_earned'] / $driver['number_of_trips'] : 0;
            $html .= '
                    <tr>
                        <td>'.htmlspecialchars($driver['driver_name'], ENT_QUOTES, 'UTF-8').'</td>
                        <td class="text-right">'.number_format($driver['number_of_trips']).'</td>
                        <td class="text-right">'.number_format($driver['total_fare_earned'], 2).'</td>
                        <td class="text-right">'.number_format($avgFarePerTrip, 2).'</td>
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
