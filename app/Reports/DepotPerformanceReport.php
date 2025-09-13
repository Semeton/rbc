<?php

namespace App\Reports;

use App\Models\DailyCustomerTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DepotPerformanceReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $depotName = $filters['depot_name'] ?? null;

        // Use database-agnostic date formatting for testing compatibility
        $dateFormat = $this->getDateFormat();

        $query = DailyCustomerTransaction::query()
            ->select([
                'origin as depot_name',
                DB::raw('COUNT(*) as total_dispatches'),
                DB::raw('SUM(atc_cost) as total_atc_cost'),
                DB::raw('SUM(transport_cost) as total_transport_cost'),
                DB::raw('SUM(atc_cost + transport_cost) as total_revenue'),
            ])
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('origin')
            ->orderBy('total_revenue', 'desc');

        if ($depotName) {
            $query->where('origin', 'like', "%{$depotName}%");
        }

        return $query->get()->map(function ($record) {
            return [
                'depot_name' => $record->depot_name,
                'total_dispatches' => (int) $record->total_dispatches,
                'total_atc_cost' => (float) $record->total_atc_cost,
                'total_transport_cost' => (float) $record->total_transport_cost,
                'total_revenue' => (float) $record->total_revenue,
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        $totalDispatches = $data->sum('total_dispatches');
        $totalRevenue = $data->sum('total_revenue');
        $totalAtcCost = $data->sum('total_atc_cost');
        $totalTransportCost = $data->sum('total_transport_cost');
        $depotCount = $data->count();

        return [
            'total_dispatches' => $totalDispatches,
            'total_revenue' => $totalRevenue,
            'total_atc_cost' => $totalAtcCost,
            'total_transport_cost' => $totalTransportCost,
            'depot_count' => $depotCount,
            'average_revenue_per_depot' => $depotCount > 0 ? $totalRevenue / $depotCount : 0,
            'average_dispatches_per_depot' => $depotCount > 0 ? $totalDispatches / $depotCount : 0,
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Prepare data for bar chart
        $chartData = $data->map(function ($depot) {
            return [
                'depot_name' => $depot['depot_name'],
                'total_revenue' => $depot['total_revenue'],
                'total_dispatches' => $depot['total_dispatches'],
                'atc_cost' => $depot['total_atc_cost'],
                'transport_cost' => $depot['total_transport_cost'],
            ];
        });

        return [
            'labels' => $chartData->pluck('depot_name')->toArray(),
            'revenue' => $chartData->pluck('total_revenue')->toArray(),
            'dispatches' => $chartData->pluck('total_dispatches')->toArray(),
            'atc_costs' => $chartData->pluck('atc_cost')->toArray(),
            'transport_costs' => $chartData->pluck('transport_cost')->toArray(),
        ];
    }

    public function getDepotList(): Collection
    {
        return DailyCustomerTransaction::query()
            ->select('origin')
            ->distinct()
            ->whereNotNull('origin')
            ->where('origin', '!=', '')
            ->orderBy('origin')
            ->pluck('origin');
    }

    private function getDateFormat(): string
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'sqlite' => "strftime('%Y-%m', date)",
            'mysql' => "DATE_FORMAT(date, '%Y-%m')",
            'pgsql' => "TO_CHAR(date, 'YYYY-MM')",
            default => "strftime('%Y-%m', date)", // Default to SQLite format for testing
        };
    }
}
