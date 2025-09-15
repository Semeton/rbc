<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DailyActivitySummaryReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        // Get daily customer transactions
        $dailyTransactions = DailyCustomerTransaction::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', true)
            ->get()
            ->groupBy(function ($transaction) {
                return Carbon::parse($transaction->date)->format('Y-m-d');
            });

        // Get daily customer payments
        $dailyPayments = CustomerPayment::query()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($payment) {
                return Carbon::parse($payment->payment_date)->format('Y-m-d');
            });

        // Get all unique dates in the range
        $allDates = collect();
        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        while ($currentDate->lte($endDateCarbon)) {
            $allDates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Combine data for each date
        return $allDates->map(function ($date) use ($dailyTransactions, $dailyPayments) {
            $transactions = $dailyTransactions->get($date, collect());
            $payments = $dailyPayments->get($date, collect());

            $totalSales = $transactions->sum(function ($transaction) {
                return $transaction->atc_cost + $transaction->transport_cost;
            });

            $totalPayments = $payments->sum('amount');
            $transactionCount = $transactions->count();
            $paymentCount = $payments->count();

            return [
                'date' => $date,
                'date_formatted' => Carbon::parse($date)->format('M d, Y'),
                'day_of_week' => Carbon::parse($date)->format('l'),
                'transaction_count' => $transactionCount,
                'payment_count' => $paymentCount,
                'total_sales' => $totalSales,
                'total_payments' => $totalPayments,
                'net_activity' => $totalPayments - $totalSales,
                'has_activity' => $transactionCount > 0 || $paymentCount > 0,
            ];
        })->sortBy('date')->values();
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        $totalTransactions = $data->sum('transaction_count');
        $totalPayments = $data->sum('payment_count');
        $totalSales = $data->sum('total_sales');
        $totalPaymentsAmount = $data->sum('total_payments');
        $netActivity = $totalPaymentsAmount - $totalSales;

        $activeDays = $data->where('has_activity', true)->count();
        $totalDays = $data->count();
        $averageTransactionsPerDay = $activeDays > 0 ? $totalTransactions / $activeDays : 0;
        $averageSalesPerDay = $activeDays > 0 ? $totalSales / $activeDays : 0;
        $averagePaymentsPerDay = $activeDays > 0 ? $totalPaymentsAmount / $activeDays : 0;

        // Find busiest day
        $busiestDay = $data->where('has_activity', true)->sortByDesc('transaction_count')->first();
        $busiestDayInfo = $busiestDay ? [
            'date' => $busiestDay['date_formatted'],
            'transactions' => $busiestDay['transaction_count'],
            'sales' => $busiestDay['total_sales'],
        ] : null;

        return [
            'total_transactions' => $totalTransactions,
            'total_payments' => $totalPayments,
            'total_sales' => $totalSales,
            'total_payments_amount' => $totalPaymentsAmount,
            'net_activity' => $netActivity,
            'active_days' => $activeDays,
            'total_days' => $totalDays,
            'average_transactions_per_day' => $averageTransactionsPerDay,
            'average_sales_per_day' => $averageSalesPerDay,
            'average_payments_per_day' => $averagePaymentsPerDay,
            'busiest_day' => $busiestDayInfo,
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Daily activity trend
        $dailyTrend = $data->map(function ($day) {
            return [
                'date' => Carbon::parse($day['date'])->format('M d'),
                'transactions' => $day['transaction_count'],
                'payments' => $day['payment_count'],
                'sales' => $day['total_sales'],
                'payments_amount' => $day['total_payments'],
                'net_activity' => $day['net_activity'],
            ];
        });

        // Weekly summary
        $weeklySummary = $data->groupBy(function ($day) {
            return Carbon::parse($day['date'])->format('Y-W');
        })->map(function ($weekData, $week) {
            return [
                'week' => 'Week '.Carbon::parse($weekData->first()['date'])->format('W'),
                'transactions' => $weekData->sum('transaction_count'),
                'payments' => $weekData->sum('payment_count'),
                'sales' => $weekData->sum('total_sales'),
                'payments_amount' => $weekData->sum('total_payments'),
                'net_activity' => $weekData->sum('net_activity'),
            ];
        })->values();

        // Activity distribution by day of week
        $dayOfWeekDistribution = $data->groupBy('day_of_week')->map(function ($dayData, $day) {
            return [
                'day' => $day,
                'transactions' => $dayData->sum('transaction_count'),
                'payments' => $dayData->sum('payment_count'),
                'sales' => $dayData->sum('total_sales'),
                'payments_amount' => $dayData->sum('total_payments'),
            ];
        })->values();

        return [
            'daily_trend' => $dailyTrend->toArray(),
            'weekly_summary' => $weeklySummary->toArray(),
            'day_of_week_distribution' => $dayOfWeekDistribution->toArray(),
        ];
    }
}
