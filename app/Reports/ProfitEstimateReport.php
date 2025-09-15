<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\DailyCustomerTransaction;
use App\Models\DailyTruckRecord;
use App\Models\TruckMaintenanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfitEstimateReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfYear()->format('Y-m-d');

        // Get daily customer transactions for revenue
        $revenueData = DailyCustomerTransaction::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', true)
            ->select(
                'date',
                DB::raw('SUM(atc_cost) as total_atc_cost'),
                DB::raw('SUM(transport_cost) as total_transport_fee'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->date)->format('Y-m-d') => $item];
            });

        // Get daily truck records for costs
        $costData = DailyTruckRecord::query()
            ->whereBetween('atc_collection_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(atc_collection_date) as date'),
                DB::raw('SUM(gas_chop_money) as total_gas_chop'),
                DB::raw('SUM(fare) as total_fare'),
                DB::raw('COUNT(*) as truck_record_count')
            )
            ->groupBy(DB::raw('DATE(atc_collection_date)'))
            ->get()
            ->keyBy('date');

        // Get maintenance costs by date
        $maintenanceData = TruckMaintenanceRecord::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(cost_of_maintenance) as total_maintenance_cost'),
                DB::raw('COUNT(*) as maintenance_count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date');

        // Get all unique dates in the range
        $allDates = collect();
        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        while ($currentDate->lte($endDateCarbon)) {
            $allDates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Combine data for each date
        return $allDates->map(function ($date) use ($revenueData, $costData, $maintenanceData) {
            $revenue = $revenueData->get($date);
            $costs = $costData->get($date);
            $maintenance = $maintenanceData->get($date);

            $totalRevenue = ($revenue ? $revenue->total_atc_cost : 0) + ($revenue ? $revenue->total_transport_fee : 0);
            $totalGasChop = $costs ? $costs->total_gas_chop : 0;
            $totalFare = $costs ? $costs->total_fare : 0;
            $totalMaintenance = $maintenance ? $maintenance->total_maintenance_cost : 0;

            $totalCosts = $totalGasChop + $totalFare + $totalMaintenance;
            $profit = $totalRevenue - $totalCosts;
            $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;

            return [
                'date' => $date,
                'date_formatted' => Carbon::parse($date)->format('M d, Y'),
                'day_of_week' => Carbon::parse($date)->format('l'),
                'total_revenue' => $totalRevenue,
                'total_atc_cost' => $revenue ? $revenue->total_atc_cost : 0,
                'total_transport_fee' => $revenue ? $revenue->total_transport_fee : 0,
                'total_costs' => $totalCosts,
                'total_gas_chop' => $totalGasChop,
                'total_fare' => $totalFare,
                'total_maintenance' => $totalMaintenance,
                'profit' => $profit,
                'profit_margin' => $profitMargin,
                'transaction_count' => $revenue ? $revenue->transaction_count : 0,
                'truck_record_count' => $costs ? $costs->truck_record_count : 0,
                'maintenance_count' => $maintenance ? $maintenance->maintenance_count : 0,
                'has_activity' => $totalRevenue > 0 || $totalCosts > 0,
            ];
        })->sortBy('date')->values();
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        $totalRevenue = $data->sum('total_revenue');
        $totalAtcCost = $data->sum('total_atc_cost');
        $totalTransportFee = $data->sum('total_transport_fee');
        $totalCosts = $data->sum('total_costs');
        $totalGasChop = $data->sum('total_gas_chop');
        $totalFare = $data->sum('total_fare');
        $totalMaintenance = $data->sum('total_maintenance');
        $totalProfit = $data->sum('profit');

        $activeDays = $data->where('has_activity', true)->count();
        $totalDays = $data->count();
        $averageProfitPerDay = $activeDays > 0 ? $totalProfit / $activeDays : 0;
        $averageRevenuePerDay = $activeDays > 0 ? $totalRevenue / $activeDays : 0;
        $averageCostsPerDay = $activeDays > 0 ? $totalCosts / $activeDays : 0;

        $overallProfitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Find most profitable day
        $mostProfitableDay = $data->where('has_activity', true)->sortByDesc('profit')->first();
        $mostProfitableDayInfo = $mostProfitableDay ? [
            'date' => $mostProfitableDay['date_formatted'],
            'profit' => $mostProfitableDay['profit'],
            'revenue' => $mostProfitableDay['total_revenue'],
        ] : null;

        // Find least profitable day
        $leastProfitableDay = $data->where('has_activity', true)->sortBy('profit')->first();
        $leastProfitableDayInfo = $leastProfitableDay ? [
            'date' => $leastProfitableDay['date_formatted'],
            'profit' => $leastProfitableDay['profit'],
            'revenue' => $leastProfitableDay['total_revenue'],
        ] : null;

        return [
            'total_revenue' => $totalRevenue,
            'total_atc_cost' => $totalAtcCost,
            'total_transport_fee' => $totalTransportFee,
            'total_costs' => $totalCosts,
            'total_gas_chop' => $totalGasChop,
            'total_fare' => $totalFare,
            'total_maintenance' => $totalMaintenance,
            'total_profit' => $totalProfit,
            'overall_profit_margin' => $overallProfitMargin,
            'active_days' => $activeDays,
            'total_days' => $totalDays,
            'average_profit_per_day' => $averageProfitPerDay,
            'average_revenue_per_day' => $averageRevenuePerDay,
            'average_costs_per_day' => $averageCostsPerDay,
            'most_profitable_day' => $mostProfitableDayInfo,
            'least_profitable_day' => $leastProfitableDayInfo,
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Monthly profit trend
        $monthlyTrend = $data->groupBy(function ($record) {
            return Carbon::parse($record['date'])->format('Y-m');
        })->map(function ($monthRecords, $month) {
            return [
                'month' => Carbon::parse($month)->format('M Y'),
                'revenue' => $monthRecords->sum('total_revenue'),
                'costs' => $monthRecords->sum('total_costs'),
                'profit' => $monthRecords->sum('profit'),
                'profit_margin' => $monthRecords->sum('total_revenue') > 0 ? ($monthRecords->sum('profit') / $monthRecords->sum('total_revenue')) * 100 : 0,
            ];
        })->sortBy('month')->values();

        // Daily profit trend
        $dailyTrend = $data->map(function ($day) {
            return [
                'date' => Carbon::parse($day['date'])->format('M d'),
                'revenue' => $day['total_revenue'],
                'costs' => $day['total_costs'],
                'profit' => $day['profit'],
                'profit_margin' => $day['profit_margin'],
            ];
        });

        // Cost breakdown
        $costBreakdown = [
            'gas_chop' => $data->sum('total_gas_chop'),
            'fare' => $data->sum('total_fare'),
            'maintenance' => $data->sum('total_maintenance'),
        ];

        // Revenue breakdown
        $revenueBreakdown = [
            'atc_cost' => $data->sum('total_atc_cost'),
            'transport_fee' => $data->sum('total_transport_fee'),
        ];

        return [
            'monthly_trend' => $monthlyTrend->toArray(),
            'daily_trend' => $dailyTrend->toArray(),
            'cost_breakdown' => $costBreakdown,
            'revenue_breakdown' => $revenueBreakdown,
        ];
    }
}
