<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MaintenanceCostReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $truckId = $filters['truck_id'] ?? null;

        $query = TruckMaintenanceRecord::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('truck');

        if ($truckId) {
            $query->where('truck_id', $truckId);
        }

        $maintenanceRecords = $query->get();

        // Group by truck for truck-level analysis
        $truckData = $maintenanceRecords->groupBy('truck_id')->map(function ($records, $truckId) {
            $truck = $records->first()->truck;
            $totalCost = $records->sum('cost_of_maintenance');
            $recordCount = $records->count();
            $averageCost = $recordCount > 0 ? $totalCost / $recordCount : 0;

            return [
                'truck_id' => $truckId,
                'registration_number' => $truck->registration_number,
                'cab_number' => $truck->cab_number,
                'truck_model' => $truck->truck_model,
                'year_of_manufacture' => $truck->year_of_manufacture,
                'total_cost' => $totalCost,
                'maintenance_count' => $recordCount,
                'average_cost_per_maintenance' => $averageCost,
                'latest_maintenance' => $records->sortByDesc('created_at')->first()->created_at,
                'maintenance_types' => $records->pluck('description')->unique()->values(),
            ];
        });

        return $truckData->sortByDesc('total_cost');
    }

    public function getSummary(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $truckId = $filters['truck_id'] ?? null;

        $query = TruckMaintenanceRecord::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($truckId) {
            $query->where('truck_id', $truckId);
        }

        $records = $query->get();

        return [
            'total_maintenance_records' => $records->count(),
            'total_cost' => $records->sum('cost_of_maintenance'),
            'average_cost_per_maintenance' => $records->avg('cost_of_maintenance'),
            'unique_trucks' => $records->pluck('truck_id')->unique()->count(),
            'most_expensive_maintenance' => $records->sortByDesc('cost_of_maintenance')->first(),
            'most_maintained_truck' => $records->groupBy('truck_id')
                ->map(fn ($truckRecords) => $truckRecords->count())
                ->sortByDesc(fn ($count) => $count)
                ->keys()
                ->first(),
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }

    public function getMonthlyTrend(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->subMonths(12)->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $truckId = $filters['truck_id'] ?? null;

        $query = TruckMaintenanceRecord::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($truckId) {
            $query->where('truck_id', $truckId);
        }

        $records = $query->get();

        // Group by month
        $monthlyData = $records->groupBy(function ($record) {
            return $record->created_at->format('Y-m');
        })->map(function ($monthRecords, $month) {
            return [
                'month' => $month,
                'month_name' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                'total_cost' => $monthRecords->sum('cost_of_maintenance'),
                'maintenance_count' => $monthRecords->count(),
                'average_cost' => $monthRecords->avg('cost_of_maintenance'),
                'unique_trucks' => $monthRecords->pluck('truck_id')->unique()->count(),
            ];
        });

        return $monthlyData->sortBy('month');
    }

    public function getMaintenanceTypes(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        $records = TruckMaintenanceRecord::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Extract maintenance types from descriptions
        $maintenanceTypes = $records->map(function ($record) {
            // Extract the first part before ' - ' as the maintenance type
            $description = $record->description;
            $type = strpos($description, ' - ') !== false
                ? explode(' - ', $description)[0]
                : $description;

            return [
                'type' => trim($type),
                'cost' => $record->cost_of_maintenance,
                'truck_id' => $record->truck_id,
            ];
        });

        // Group by type
        return $maintenanceTypes->groupBy('type')->map(function ($typeRecords, $type) {
            return [
                'type' => $type,
                'total_cost' => $typeRecords->sum('cost'),
                'count' => $typeRecords->count(),
                'average_cost' => $typeRecords->avg('cost'),
                'unique_trucks' => $typeRecords->pluck('truck_id')->unique()->count(),
            ];
        })->sortByDesc('total_cost');
    }

    public function getTopCostlyTrucks(array $filters = [], int $limit = 10): Collection
    {
        return $this->generate($filters)->take($limit);
    }

    public function getMaintenanceFrequency(array $filters = []): Collection
    {
        $data = $this->generate($filters);

        return $data->map(function ($truck) {
            $truck['maintenance_frequency'] = $truck['maintenance_count'] > 0
                ? $truck['maintenance_count'] / 30 // Assuming 30-day period
                : 0;

            return $truck;
        })->sortByDesc('maintenance_frequency');
    }
}
