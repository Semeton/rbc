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

        $customers = $query->with(['transactions', 'payments'])->get();

        return $customers->map(function (Customer $customer) use ($startDate, $endDate) {
            // Calculate total transactions (debits)
            $totalTransactions = $customer->transactions()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });

            // Calculate total payments (credits)
            $totalPayments = $customer->payments()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount');

            // Calculate opening balance (transactions before start date)
            $openingBalance = $customer->transactions()
                ->where('created_at', '<', $startDate)
                ->get()
                ->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                }) - $customer->payments()
                ->where('payment_date', '<', $startDate)
                ->sum('amount');

            // Calculate closing balance
            $closingBalance = $openingBalance + $totalTransactions - $totalPayments;

            return [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'opening_balance' => $openingBalance,
                'total_transactions' => $totalTransactions,
                'total_payments' => $totalPayments,
                'closing_balance' => $closingBalance,
                'transaction_count' => $customer->transactions()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'payment_count' => $customer->payments()
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->count(),
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_customers' => $data->count(),
            'total_opening_balance' => $data->sum('opening_balance'),
            'total_transactions' => $data->sum('total_transactions'),
            'total_payments' => $data->sum('total_payments'),
            'total_closing_balance' => $data->sum('closing_balance'),
            'average_balance' => $data->avg('closing_balance'),
            'customers_with_balance' => $data->where('closing_balance', '>', 0)->count(),
            'customers_with_debt' => $data->where('closing_balance', '<', 0)->count(),
        ];
    }
}
