<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerBalanceReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $customerId = $filters['customer_id'] ?? null;

        $query = Customer::query();

        if ($customerId) {
            $query->where('id', $customerId);
        }

        return $query->get()->map(function ($customer) use ($startDate, $endDate) {
            // Calculate total ATC value (transactions within date range)
            $totalAtcValue = $customer->transactions()
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });

            // Calculate total payments (within date range)
            $totalPayments = $customer->payments()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount');

            // Calculate outstanding balance (using ALL transactions and payments for accurate balance)
            $totalAllTransactions = $customer->transactions()
                ->get()
                ->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });
            
            $totalAllPayments = $customer->payments()->sum('amount');
            $outstandingBalance = $totalAllPayments - $totalAllTransactions;

            return [
                'customer_name' => $customer->name,
                'total_atc_value' => $totalAtcValue, // Period-specific ATC value
                'total_payments' => $totalPayments, // Period-specific payments
                'total_all_transactions' => $totalAllTransactions, // All-time transactions
                'total_all_payments' => $totalAllPayments, // All-time payments
                'outstanding_balance' => $outstandingBalance, // All-time outstanding balance
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_customers' => $data->count(),
            'total_atc_value' => $data->sum('total_atc_value'), // Period-specific ATC value
            'total_payments' => $data->sum('total_payments'), // Period-specific payments
            'total_outstanding_balance' => $data->sum('outstanding_balance'), // All-time outstanding balance
            'customers_with_debt' => $data->where('outstanding_balance', '<', 0)->count(), // Negative balance = owes money
            'customers_with_credit' => $data->where('outstanding_balance', '>', 0)->count(), // Positive balance = overpaid
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'labels' => $data->pluck('customer_name')->toArray(),
            'atc_values' => $data->pluck('total_atc_value')->toArray(),
            'payments' => $data->pluck('total_payments')->toArray(),
            'outstanding_balances' => $data->pluck('outstanding_balance')->toArray(),
        ];
    }
}
