<?php

declare(strict_types=1);

namespace App\Actions;

use App\Reports\TruckMaintenanceCostReport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class ExportTruckMaintenanceCostPdf
{
    public function execute(array $filters = []): Response
    {
        $report = new TruckMaintenanceCostReport;
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

        $filename = 'truck-maintenance-cost-'.now()->format('Y-m-d_H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
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
            <title>Truck Maintenance Cost Report</title>
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
                <h1>Truck Maintenance Cost Report</h1>
                <p>Generated on: '.now()->format('F d, Y').'</p>
                <p>Period: '.\Carbon\Carbon::parse($startDate)->format('M d, Y').' - '.\Carbon\Carbon::parse($endDate)->format('M d, Y').'</p>
                <p>Truck Filter: '.htmlspecialchars($truckName, ENT_QUOTES, 'UTF-8').'</p>
            </div>

            <div class="summary">
                <div class="summary-card">
                    <div class="summary-value">N'.number_format($summary['total_maintenance_cost'], 2).'</div>
                    <div class="summary-label">Total Maintenance Cost</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['total_records']).'</div>
                    <div class="summary-label">Total Records</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">'.number_format($summary['unique_trucks']).'</div>
                    <div class="summary-label">Trucks Maintained</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">N'.number_format($summary['average_cost_per_record'], 2).'</div>
                    <div class="summary-label">Avg Cost per Record</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Truck</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-right">Cost (₦)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($data as $record) {
            $html .= '
                    <tr>
                        <td>'.htmlspecialchars($record['truck_cab_number'].' - '.$record['truck_registration_number'], ENT_QUOTES, 'UTF-8').'</td>
                        <td>'.\Carbon\Carbon::parse($record['date'])->format('M d, Y').'</td>
                        <td>'.htmlspecialchars($record['description'], ENT_QUOTES, 'UTF-8').'</td>
                        <td class="text-right">'.number_format((float) $record['maintenance_cost'], 2).'</td>
                        <td class="text-center">'.htmlspecialchars($record['status'], ENT_QUOTES, 'UTF-8').'</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                <p>This report was generated by RBC Trucking Management System</p>
                <p>Highest Maintenance Truck: '.htmlspecialchars($summary['highest_maintenance_truck'], ENT_QUOTES, 'UTF-8').' (N'.number_format($summary['highest_maintenance_cost'], 2).')</p>
                <p>Average Cost per Truck: N'.number_format($summary['average_cost_per_truck'], 2).'</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
