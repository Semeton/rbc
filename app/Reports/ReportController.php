<?php

declare(strict_types=1);

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController
{
    public function __construct(
        private CustomerBalanceReport $customerBalanceReport,
        private MonthlySalesReport $monthlySalesReport,
        private DriverPerformanceReport $driverPerformanceReport,
        private TruckUtilizationReport $truckUtilizationReport,
        private MaintenanceCostReport $maintenanceCostReport
    ) {}

    public function index(): View
    {
        return view('reports.index');
    }

    public function customerBalance(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $data = $this->customerBalanceReport->generate($filters);
        $summary = $this->customerBalanceReport->getSummary($filters);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function monthlySales(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $data = $this->monthlySalesReport->generate($filters);
        $summary = $this->monthlySalesReport->getSummary($filters);
        $topCustomers = $this->monthlySalesReport->getTopCustomers($filters, 10);
        $topDrivers = $this->monthlySalesReport->getTopDrivers($filters, 10);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'top_customers' => $topCustomers,
            'top_drivers' => $topDrivers,
            'filters' => $filters,
        ]);
    }

    public function driverPerformance(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $data = $this->driverPerformanceReport->generate($filters);
        $summary = $this->driverPerformanceReport->getSummary($filters);
        $topPerformers = $this->driverPerformanceReport->getTopPerformers($filters, 10);
        $mostEfficient = $this->driverPerformanceReport->getMostEfficient($filters, 10);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'top_performers' => $topPerformers,
            'most_efficient' => $mostEfficient,
            'filters' => $filters,
        ]);
    }

    public function truckUtilization(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $data = $this->truckUtilizationReport->generate($filters);
        $summary = $this->truckUtilizationReport->getSummary($filters);
        $topUtilized = $this->truckUtilizationReport->getTopUtilized($filters, 10);
        $topRevenue = $this->truckUtilizationReport->getTopRevenue($filters, 10);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'top_utilized' => $topUtilized,
            'top_revenue' => $topRevenue,
            'filters' => $filters,
        ]);
    }

    public function maintenanceCost(Request $request): JsonResponse
    {
        $filters = $this->extractFilters($request);
        $data = $this->maintenanceCostReport->generate($filters);
        $summary = $this->maintenanceCostReport->getSummary($filters);
        $monthlyTrend = $this->maintenanceCostReport->getMonthlyTrend($filters);
        $maintenanceTypes = $this->maintenanceCostReport->getMaintenanceTypes($filters);

        return response()->json([
            'data' => $data,
            'summary' => $summary,
            'monthly_trend' => $monthlyTrend,
            'maintenance_types' => $maintenanceTypes,
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
