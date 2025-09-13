<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\DailyCustomerTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MonthlySalesReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $customerId = $filters['customer_id'] ?? null;

        // Use database-agnostic date formatting for testing compatibility
        $dateFormat = $this->getDateFormat();

        $query = DailyCustomerTransaction::query()
            ->select([
                DB::raw($dateFormat.' as month'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(atc_cost) as total_atc_cost'),
                DB::raw('SUM(transport_cost) as total_transport_fees'),
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw($dateFormat))
            ->orderBy('month');

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        return $query->get()->map(function ($record) {
            return [
                'month' => $record->month,
                'month_name' => \Carbon\Carbon::createFromFormat('Y-m', $record->month)->format('F Y'),
                'total_transactions' => (int) $record->total_transactions,
                'total_atc_cost' => (float) $record->total_atc_cost,
                'total_transport_fees' => (float) $record->total_transport_fees,
                'total_revenue' => (float) $record->total_atc_cost + (float) $record->total_transport_fees,
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_months' => $data->count(),
            'total_transactions' => $data->sum('total_transactions'),
            'total_atc_cost' => $data->sum('total_atc_cost'),
            'total_transport_fees' => $data->sum('total_transport_fees'),
            'total_revenue' => $data->sum('total_revenue'),
            'average_monthly_revenue' => $data->count() > 0 ? $data->avg('total_revenue') : 0,
            'best_month' => $data->sortByDesc('total_revenue')->first(),
            'worst_month' => $data->sortBy('total_revenue')->first(),
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'monthly_revenue' => [
                'labels' => $data->pluck('month_name')->toArray(),
                'revenue' => $data->pluck('total_revenue')->toArray(),
                'atc_cost' => $data->pluck('total_atc_cost')->toArray(),
                'transport_fees' => $data->pluck('total_transport_fees')->toArray(),
            ],
            'cement_distribution' => $this->getCementTypeDistribution($filters),
        ];
    }

    private function getCementTypeDistribution(array $filters): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $customerId = $filters['customer_id'] ?? null;

        $query = DailyCustomerTransaction::query()
            ->select([
                'cement_type',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(atc_cost) as total_cost'),
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('cement_type')
            ->orderByDesc('total_transactions');

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $distribution = $query->get();

        return [
            'labels' => $distribution->pluck('cement_type')->toArray(),
            'transactions' => $distribution->pluck('total_transactions')->toArray(),
            'costs' => $distribution->pluck('total_cost')->toArray(),
        ];
    }

    private function getDateFormat(): string
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'mysql' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            default => "strftime('%Y-%m', created_at)", // Default to SQLite format for testing
        };
    }
}
