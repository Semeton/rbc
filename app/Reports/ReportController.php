<?php

declare(strict_types=1);

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function customerBalance(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $report = app(CustomerBalanceReport::class);
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function monthlySales(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $report = app(MonthlySalesReport::class);
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function driverPerformance(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $report = app(DriverPerformanceReport::class);
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function truckUtilization(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $report = app(TruckUtilizationReport::class);
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function maintenanceCost(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $report = app(MaintenanceCostReport::class);
        $data = $report->generate($filters);
        $summary = $report->getSummary($filters);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function export(Request $request, string $reportType): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $format = $request->get('format', 'excel');

        // TODO: Implement export functionality
        // This would integrate with Laravel Excel for Excel export
        // and DomPDF for PDF export

        return response()->json([
            'message' => 'Export functionality will be implemented',
            'report_type' => $reportType,
            'format' => $format,
            'filters' => $filters,
        ]);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer_id' => $request->get('customer_id'),
            'driver_id' => $request->get('driver_id'),
            'truck_id' => $request->get('truck_id'),
        ];
    }
}
