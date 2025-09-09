<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TruckUtilizationReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $truckId = $filters['truck_id'] ?? null;

        $query = Truck::query();

        if ($truckId) {
            $query->where('id', $truckId);
        }

        $trucks = $query->with(['truckRecords', 'maintenanceRecords'])->get();

        return $trucks->map(function (Truck $truck) use ($startDate, $endDate) {
            // Get truck movements in period
            $movements = $truck->truckRecords()
                ->whereBetween('atc_collection_date', [$startDate, $endDate])
                ->get();

            // Get maintenance records in period
            $maintenanceRecords = $truck->maintenanceRecords()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Calculate utilization metrics
            $totalTrips = $movements->count();
            $totalDistance = 0; // Not available in current schema
            $totalFuelCost = $movements->sum('gas_chop_money');
            $totalMaintenanceCost = $maintenanceRecords->sum('cost_of_maintenance');
            $totalRevenue = $movements->sum('fare');

            // Calculate working days
            $workingDays = $movements->pluck('atc_collection_date')
                ->map(fn ($date) => $date->format('Y-m-d'))
                ->unique()
                ->count();

            // Calculate period days
            $periodDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

            // Calculate utilization percentage
            $utilizationPercentage = $periodDays > 0 ? ($workingDays / $periodDays) * 100 : 0;

            // Calculate efficiency metrics
            $revenuePerKm = $totalDistance > 0 ? $totalRevenue / $totalDistance : 0;
            $fuelEfficiency = $totalDistance > 0 ? $totalFuelCost / $totalDistance : 0;
            $maintenanceCostPerKm = $totalDistance > 0 ? $totalMaintenanceCost / $totalDistance : 0;
            $tripsPerDay = $workingDays > 0 ? $totalTrips / $workingDays : 0;

            return [
                'truck_id' => $truck->id,
                'registration_number' => $truck->registration_number,
                'cab_number' => $truck->cab_number,
                'truck_model' => $truck->truck_model,
                'year_of_manufacture' => $truck->year_of_manufacture,
                'status' => $truck->status_string,
                'total_trips' => $totalTrips,
                'total_distance_km' => $totalDistance,
                'total_fuel_cost' => $totalFuelCost,
                'total_maintenance_cost' => $totalMaintenanceCost,
                'total_revenue' => $totalRevenue,
                'working_days' => $workingDays,
                'period_days' => $periodDays,
                'utilization_percentage' => $utilizationPercentage,
                'revenue_per_km' => $revenuePerKm,
                'fuel_efficiency' => $fuelEfficiency,
                'maintenance_cost_per_km' => $maintenanceCostPerKm,
                'trips_per_day' => $tripsPerDay,
                'maintenance_count' => $maintenanceRecords->count(),
                'net_profit' => $totalRevenue - $totalFuelCost - $totalMaintenanceCost,
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_trucks' => $data->count(),
            'active_trucks' => $data->where('status', 'active')->count(),
            'total_trips' => $data->sum('total_trips'),
            'total_distance' => $data->sum('total_distance_km'),
            'total_fuel_cost' => $data->sum('total_fuel_cost'),
            'total_maintenance_cost' => $data->sum('total_maintenance_cost'),
            'total_revenue' => $data->sum('total_revenue'),
            'total_net_profit' => $data->sum('net_profit'),
            'average_utilization' => $data->avg('utilization_percentage'),
            'average_revenue_per_truck' => $data->avg('total_revenue'),
            'most_utilized' => $data->sortByDesc('utilization_percentage')->first(),
            'highest_revenue' => $data->sortByDesc('total_revenue')->first(),
            'most_efficient' => $data->sortByDesc('revenue_per_km')->first(),
        ];
    }

    public function getTopUtilized(array $filters = [], int $limit = 10): Collection
    {
        return $this->generate($filters)
            ->sortByDesc('utilization_percentage')
            ->take($limit);
    }

    public function getTopRevenue(array $filters = [], int $limit = 10): Collection
    {
        return $this->generate($filters)
            ->sortByDesc('total_revenue')
            ->take($limit);
    }

    public function getMostEfficient(array $filters = [], int $limit = 10): Collection
    {
        return $this->generate($filters)
            ->sortByDesc('revenue_per_km')
            ->take($limit);
    }

    public function getMaintenanceAnalysis(array $filters = []): Collection
    {
        return $this->generate($filters)
            ->sortByDesc('maintenance_cost_per_km')
            ->take(10);
    }
}
