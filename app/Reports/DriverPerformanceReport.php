<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\DailyCustomerTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DriverPerformanceReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $driverId = $filters['driver_id'] ?? null;

        $query = DailyCustomerTransaction::query()
            ->join('drivers', 'daily_customer_transactions.driver_id', '=', 'drivers.id')
            ->select([
                'drivers.name as driver_name',
                'drivers.id as driver_id',
                DB::raw('COUNT(*) as number_of_trips'),
                DB::raw('SUM(transport_cost) as total_fare_earned'),
            ])
            ->whereBetween('daily_customer_transactions.date', [$startDate, $endDate])
            ->where('daily_customer_transactions.status', 1) // Only completed trips
            ->groupBy('drivers.id', 'drivers.name')
            ->orderBy('total_fare_earned', 'desc');

        if ($driverId) {
            $query->where('drivers.id', $driverId);
        }

        return $query->get()->map(function ($record) {
            return [
                'driver_id' => $record->driver_id,
                'driver_name' => $record->driver_name,
                'number_of_trips' => (int) $record->number_of_trips,
                'total_fare_earned' => (float) $record->total_fare_earned,
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_drivers' => $data->count(),
            'total_trips' => $data->sum('number_of_trips'),
            'total_fare_earned' => $data->sum('total_fare_earned'),
            'average_trips_per_driver' => $data->avg('number_of_trips'),
            'average_fare_per_driver' => $data->avg('total_fare_earned'),
            'top_performer' => $data->first()['driver_name'] ?? 'N/A',
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Trip trends over time
        $tripTrends = $this->getTripTrends($filters);

        // Top performers for bar chart
        $topPerformers = $data->take(10); // Top 10 drivers

        return [
            'trip_trends' => [
                'labels' => $tripTrends->keys()->toArray(),
                'trips' => $tripTrends->values()->toArray(),
            ],
            'top_performers' => [
                'labels' => $topPerformers->pluck('driver_name')->toArray(),
                'trips' => $topPerformers->pluck('number_of_trips')->toArray(),
                'fare_earned' => $topPerformers->pluck('total_fare_earned')->toArray(),
            ],
        ];
    }

    public function getTripTrends(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $driverId = $filters['driver_id'] ?? null;

        // Use database-agnostic date formatting for testing compatibility
        $dateFormat = $this->getDateFormat();

        $query = DailyCustomerTransaction::query()
            ->select([
                DB::raw($dateFormat.' as month'),
                DB::raw('COUNT(*) as total_trips'),
            ])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 1)
            ->groupBy(DB::raw($dateFormat))
            ->orderBy('month');

        if ($driverId) {
            $query->where('driver_id', $driverId);
        }

        return $query->get()->mapWithKeys(function ($record) {
            $monthKey = $record->month;
            $monthName = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('M Y');

            return [$monthName => (int) $record->total_trips];
        });
    }

    public function getDriverList(): Collection
    {
        return \App\Models\Driver::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);
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
