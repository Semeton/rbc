<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\DailyCustomerTransaction;
use Illuminate\Support\Collection;

class MonthlySalesReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $customerId = $filters['customer_id'] ?? null;
        $driverId = $filters['driver_id'] ?? null;

        $query = DailyCustomerTransaction::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'driver', 'atc']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($driverId) {
            $query->where('driver_id', $driverId);
        }

        $transactions = $query->get();

        // Group by date for daily breakdown
        $dailyData = $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function ($dayTransactions, $date) {
            $totalAmount = $dayTransactions->sum(function ($transaction) {
                return $transaction->atc_cost + $transaction->transport_cost;
            });
            $totalAtcCost = $dayTransactions->sum('atc_cost');
            $totalTransportCost = $dayTransactions->sum('transport_cost');

            return [
                'date' => $date,
                'transaction_count' => $dayTransactions->count(),
                'total_amount' => $totalAmount,
                'total_atc_cost' => $totalAtcCost,
                'total_transport_cost' => $totalTransportCost,
                'net_profit' => $totalAmount - $totalAtcCost - $totalTransportCost,
                'customers' => $dayTransactions->pluck('customer.name')->unique()->count(),
                'drivers' => $dayTransactions->pluck('driver.name')->unique()->count(),
            ];
        });

        return $dailyData->sortBy('date');
    }

    public function getSummary(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $customerId = $filters['customer_id'] ?? null;
        $driverId = $filters['driver_id'] ?? null;

        $query = DailyCustomerTransaction::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($driverId) {
            $query->where('driver_id', $driverId);
        }

        $transactions = $query->get();

        $totalRevenue = $transactions->sum(function ($transaction) {
            return $transaction->atc_cost + $transaction->transport_cost;
        });

        return [
            'total_transactions' => $transactions->count(),
            'total_revenue' => $totalRevenue,
            'total_atc_cost' => $transactions->sum('atc_cost'),
            'total_transport_cost' => $transactions->sum('transport_cost'),
            'net_profit' => $totalRevenue - $transactions->sum('atc_cost') - $transactions->sum('transport_cost'),
            'average_transaction_value' => $transactions->count() > 0 ? $totalRevenue / $transactions->count() : 0,
            'unique_customers' => $transactions->pluck('customer_id')->unique()->count(),
            'unique_drivers' => $transactions->pluck('driver_id')->unique()->count(),
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }

    public function getTopCustomers(array $filters = [], int $limit = 10): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        return DailyCustomerTransaction::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('customer')
            ->get()
            ->groupBy('customer_id')
            ->map(function ($transactions, $customerId) {
                $customer = $transactions->first()->customer;
                $totalAmount = $transactions->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });

                return [
                    'customer_id' => $customerId,
                    'customer_name' => $customer->name,
                    'transaction_count' => $transactions->count(),
                    'total_amount' => $totalAmount,
                    'average_transaction' => $transactions->count() > 0 ? $totalAmount / $transactions->count() : 0,
                ];
            })
            ->sortByDesc('total_amount')
            ->take($limit);
    }

    public function getTopDrivers(array $filters = [], int $limit = 10): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        return DailyCustomerTransaction::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('driver')
            ->get()
            ->groupBy('driver_id')
            ->map(function ($transactions, $driverId) {
                $driver = $transactions->first()->driver;
                $totalAmount = $transactions->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });

                return [
                    'driver_id' => $driverId,
                    'driver_name' => $driver->name,
                    'transaction_count' => $transactions->count(),
                    'total_amount' => $totalAmount,
                    'average_transaction' => $transactions->count() > 0 ? $totalAmount / $transactions->count() : 0,
                ];
            })
            ->sortByDesc('total_amount')
            ->take($limit);
    }
}
