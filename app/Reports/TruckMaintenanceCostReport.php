<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use Illuminate\Support\Collection;

class TruckMaintenanceCostReport
{
    public function generate(array $filters = []): Collection
    {
        $query = TruckMaintenanceRecord::with('truck')
            ->where('status', 1); // Only completed maintenance records

        // Apply date range filter
        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Apply truck filter
        if (! empty($filters['truck_id'])) {
            $query->where('truck_id', $filters['truck_id']);
        }

        $maintenanceRecords = $query->orderBy('created_at', 'desc')->get();

        return $maintenanceRecords->map(function ($record) {
            return [
                'id' => $record->id,
                'truck_cab_number' => $record->truck->cab_number ?? 'N/A',
                'truck_registration_number' => $record->truck->registration_number ?? 'N/A',
                'truck_model' => $record->truck->truck_model ?? 'N/A',
                'date' => $record->created_at->format('Y-m-d'),
                'maintenance_cost' => $record->cost_of_maintenance,
                'description' => $record->description,
                'status' => $record->status ? 'Completed' : 'Pending',
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        $totalMaintenanceCost = $data->sum('maintenance_cost');
        $totalRecords = $data->count();
        $uniqueTrucks = $data->pluck('truck_cab_number')->unique()->count();
        $averageCostPerRecord = $totalRecords > 0 ? $totalMaintenanceCost / $totalRecords : 0;
        $averageCostPerTruck = $uniqueTrucks > 0 ? $totalMaintenanceCost / $uniqueTrucks : 0;

        // Find truck with highest maintenance cost
        $truckCosts = $data->groupBy('truck_cab_number')
            ->map(function ($records) {
                return $records->sum('maintenance_cost');
            });

        $highestMaintenanceTruck = $truckCosts->isNotEmpty()
            ? $truckCosts->sortDesc()->keys()->first()
            : 'N/A';

        $highestMaintenanceCost = $truckCosts->isNotEmpty()
            ? $truckCosts->sortDesc()->first()
            : 0;

        return [
            'total_maintenance_cost' => $totalMaintenanceCost,
            'total_records' => $totalRecords,
            'unique_trucks' => $uniqueTrucks,
            'average_cost_per_record' => $averageCostPerRecord,
            'average_cost_per_truck' => $averageCostPerTruck,
            'highest_maintenance_truck' => $highestMaintenanceTruck,
            'highest_maintenance_cost' => $highestMaintenanceCost,
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Group by month for trend analysis
        $monthlyData = $data->groupBy(function ($record) {
            return \Carbon\Carbon::parse($record['date'])->format('Y-m');
        })->map(function ($records) {
            return $records->sum('maintenance_cost');
        })->sortKeys();

        // Group by truck for comparison
        $truckData = $data->groupBy('truck_cab_number')
            ->map(function ($records) {
                return $records->sum('maintenance_cost');
            })->sortDesc();

        return [
            'monthly_trend' => [
                'labels' => $monthlyData->keys()->toArray(),
                'data' => $monthlyData->values()->toArray(),
            ],
            'truck_comparison' => [
                'labels' => $truckData->keys()->toArray(),
                'data' => $truckData->values()->toArray(),
            ],
        ];
    }

    public function getTruckList(): Collection
    {
        return Truck::select('id', 'cab_number', 'registration_number')
            ->orderBy('cab_number')
            ->get();
    }
}
