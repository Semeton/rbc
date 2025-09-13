<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\DailyTruckRecord;
use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use Illuminate\Support\Collection;

class TruckUtilizationReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $truckId = $filters['truck_id'] ?? null;

        $query = Truck::query();

        if ($truckId) {
            $query->where('id', $truckId);
        }

        return $query->get()->map(function ($truck) use ($startDate, $endDate) {
            // Get truck records for this truck within the date range
            $truckRecords = DailyTruckRecord::where('truck_id', $truck->id)
                ->where('status', 1) // Only completed records
                ->whereBetween('atc_collection_date', [$startDate, $endDate])
                ->get();

            // Get maintenance records for this truck within the date range
            $maintenanceRecords = TruckMaintenanceRecord::where('truck_id', $truck->id)
                ->where('status', 1) // Only completed maintenance
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $totalTrips = $truckRecords->count();
            $totalIncomeGenerated = $truckRecords->sum('fare');
            $totalGasChopMoney = $truckRecords->sum('gas_chop_money');
            $totalBalance = $truckRecords->sum('balance');
            $totalMaintenanceCost = $maintenanceRecords->sum('cost_of_maintenance');
            $totalMaintenanceRecords = $maintenanceRecords->count();

            return [
                'truck_id' => $truck->id,
                'cab_number' => $truck->cab_number,
                'registration_number' => $truck->registration_number,
                'truck_model' => $truck->truck_model,
                'year_of_manufacture' => $truck->year_of_manufacture,
                'truck_status' => $truck->status,
                'total_trips' => (int) $totalTrips,
                'total_income_generated' => (float) $totalIncomeGenerated,
                'total_gas_chop_money' => (float) $totalGasChopMoney,
                'total_balance' => (float) $totalBalance,
                'total_maintenance_cost' => (float) $totalMaintenanceCost,
                'total_maintenance_records' => (int) $totalMaintenanceRecords,
                'utilization_status' => $this->getUtilizationStatus($totalTrips, $totalIncomeGenerated, $totalMaintenanceCost),
            ];
        })->sortByDesc('total_income_generated');
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_trucks' => $data->count(),
            'active_trucks' => $data->where('truck_status', 1)->count(),
            'total_trips' => $data->sum('total_trips'),
            'total_income_generated' => $data->sum('total_income_generated'),
            'total_gas_chop_money' => $data->sum('total_gas_chop_money'),
            'total_balance' => $data->sum('total_balance'),
            'total_maintenance_cost' => $data->sum('total_maintenance_cost'),
            'total_maintenance_records' => $data->sum('total_maintenance_records'),
            'average_trips_per_truck' => $data->avg('total_trips'),
            'average_income_per_truck' => $data->avg('total_income_generated'),
            'average_maintenance_cost_per_truck' => $data->avg('total_maintenance_cost'),
            'most_utilized_truck' => $data->sortByDesc('total_income_generated')->first()['cab_number'] ?? 'N/A',
            'most_maintained_truck' => $data->sortByDesc('total_maintenance_cost')->first()['cab_number'] ?? 'N/A',
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Income distribution
        $incomeDistribution = $data->sortByDesc('total_income_generated')->take(10);

        // Trip distribution
        $tripDistribution = $data->sortByDesc('total_trips')->take(10);

        // Maintenance cost distribution
        $maintenanceDistribution = $data->sortByDesc('total_maintenance_cost')->take(10);

        // Truck age vs income correlation
        $ageIncomeData = $data->map(function ($truck) {
            $age = now()->year - $truck['year_of_manufacture'];

            return [
                'cab_number' => $truck['cab_number'],
                'age' => $age,
                'income' => $truck['total_income_generated'],
                'trips' => $truck['total_trips'],
            ];
        })->sortBy('age');

        return [
            'income_distribution' => [
                'labels' => $incomeDistribution->pluck('cab_number')->toArray(),
                'income' => $incomeDistribution->pluck('total_income_generated')->toArray(),
            ],
            'trip_distribution' => [
                'labels' => $tripDistribution->pluck('cab_number')->toArray(),
                'trips' => $tripDistribution->pluck('total_trips')->toArray(),
            ],
            'maintenance_distribution' => [
                'labels' => $maintenanceDistribution->pluck('cab_number')->toArray(),
                'costs' => $maintenanceDistribution->pluck('total_maintenance_cost')->toArray(),
            ],
            'age_income_correlation' => [
                'labels' => $ageIncomeData->pluck('cab_number')->toArray(),
                'ages' => $ageIncomeData->pluck('age')->toArray(),
                'income' => $ageIncomeData->pluck('income')->toArray(),
                'trips' => $ageIncomeData->pluck('trips')->toArray(),
            ],
        ];
    }

    public function getTruckList(): Collection
    {
        return Truck::where('status', 1)
            ->orderBy('cab_number')
            ->get(['id', 'cab_number', 'registration_number']);
    }

    private function getUtilizationStatus(int $totalTrips, float $totalIncomeGenerated, float $totalMaintenanceCost): string
    {
        if ($totalTrips === 0) {
            return 'Not Utilized';
        }

        $incomePerTrip = $totalIncomeGenerated / $totalTrips;
        $maintenanceRatio = $totalMaintenanceCost > 0 ? $totalIncomeGenerated / $totalMaintenanceCost : 0;

        if ($totalTrips >= 20 && $incomePerTrip >= 5000 && $maintenanceRatio >= 3) {
            return 'Highly Utilized';
        } elseif ($totalTrips >= 10 && $incomePerTrip >= 3000 && $maintenanceRatio >= 2) {
            return 'Well Utilized';
        } elseif ($totalTrips >= 5 && $incomePerTrip >= 2000) {
            return 'Moderately Utilized';
        } else {
            return 'Under Utilized';
        }
    }
}
