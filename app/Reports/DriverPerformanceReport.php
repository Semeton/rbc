<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Driver;
use Illuminate\Support\Collection;

class DriverPerformanceReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $driverId = $filters['driver_id'] ?? null;

        $query = Driver::query();

        if ($driverId) {
            $query->where('id', $driverId);
        }

        $drivers = $query->with(['transactions', 'truckRecords'])->get();

        return $drivers->map(function (Driver $driver) use ($startDate, $endDate) {
            // Get transactions in period
            $transactions = $driver->transactions()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Get truck movements in period
            $truckMovements = $driver->truckRecords()
                ->whereBetween('atc_collection_date', [$startDate, $endDate])
                ->get();

            // Calculate performance metrics
            $totalRevenue = $transactions->sum(function ($transaction) {
                return $transaction->atc_cost + $transaction->transport_cost;
            });
            $totalTrips = $transactions->count();
            $totalTons = $transactions->sum('tons');
            $totalDistance = 0; // Not available in current schema
            $totalFuelCost = $truckMovements->sum('gas_chop_money');

            // Calculate efficiency metrics
            $revenuePerTrip = $totalTrips > 0 ? $totalRevenue / $totalTrips : 0;
            $revenuePerTon = $totalTons > 0 ? $totalRevenue / $totalTons : 0;
            $revenuePerKm = $totalDistance > 0 ? $totalRevenue / $totalDistance : 0;
            $fuelEfficiency = $totalDistance > 0 ? $totalFuelCost / $totalDistance : 0;

            // Calculate working days
            $workingDays = $transactions->pluck('created_at')
                ->map(fn ($date) => $date->format('Y-m-d'))
                ->unique()
                ->count();

            return [
                'driver_id' => $driver->id,
                'driver_name' => $driver->name,
                'driver_phone' => $driver->phone,
                'license_number' => $driver->license_number,
                'total_revenue' => $totalRevenue,
                'total_trips' => $totalTrips,
                'total_tons' => $totalTons,
                'total_distance_km' => $totalDistance,
                'total_fuel_cost' => $totalFuelCost,
                'working_days' => $workingDays,
                'revenue_per_trip' => $revenuePerTrip,
                'revenue_per_ton' => $revenuePerTon,
                'revenue_per_km' => $revenuePerKm,
                'fuel_efficiency' => $fuelEfficiency,
                'average_trip_value' => $totalTrips > 0 ? $totalRevenue / $totalTrips : 0,
                'average_tons_per_trip' => $totalTrips > 0 ? $totalTons / $totalTrips : 0,
                'trips_per_day' => $workingDays > 0 ? $totalTrips / $workingDays : 0,
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_drivers' => $data->count(),
            'total_revenue' => $data->sum('total_revenue'),
            'total_trips' => $data->sum('total_trips'),
            'total_tons' => $data->sum('total_tons'),
            'total_distance' => $data->sum('total_distance_km'),
            'total_fuel_cost' => $data->sum('total_fuel_cost'),
            'average_revenue_per_driver' => $data->avg('total_revenue'),
            'average_trips_per_driver' => $data->avg('total_trips'),
            'average_revenue_per_trip' => $data->avg('revenue_per_trip'),
            'top_performer' => $data->sortByDesc('total_revenue')->first(),
            'most_efficient' => $data->sortByDesc('revenue_per_km')->first(),
        ];
    }

    public function getTopPerformers(array $filters = [], int $limit = 10): Collection
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

    public function getDriverRanking(array $filters = []): Collection
    {
        $data = $this->generate($filters);

        return $data->map(function ($driver, $index) {
            $driver['rank'] = $index + 1;

            return $driver;
        })->sortByDesc('total_revenue');
    }
}
