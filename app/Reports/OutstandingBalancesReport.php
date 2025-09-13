<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Customer;
use Illuminate\Support\Collection;

class OutstandingBalancesReport
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
            // Calculate outstanding amount (only show customers who owe money)
            $totalTransactions = $customer->transactions()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });

            $totalPayments = $customer->payments()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount');

            $outstandingAmount = $totalTransactions - $totalPayments;

            // Only include customers who owe money
            if ($outstandingAmount <= 0) {
                return null;
            }

            // Get last payment date
            $lastPayment = $customer->payments()
                ->orderBy('payment_date', 'desc')
                ->first();

            return [
                'customer_name' => $customer->name,
                'last_payment_date' => $lastPayment ? $lastPayment->payment_date : null,
                'outstanding_amount' => $outstandingAmount,
            ];
        })->filter(); // Remove null entries
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'total_customers_with_debt' => $data->count(),
            'total_outstanding_amount' => $data->sum('outstanding_amount'),
            'average_outstanding_amount' => $data->count() > 0 ? $data->avg('outstanding_amount') : 0,
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        return [
            'labels' => $data->pluck('customer_name')->toArray(),
            'outstanding_amounts' => $data->pluck('outstanding_amount')->toArray(),
        ];
    }
}
