<?php

namespace App\Reports;

use App\Models\CustomerPayment;
use Illuminate\Support\Collection;

class CustomerPaymentHistoryReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $customerId = $filters['customer_id'] ?? null;
        $paymentType = $filters['payment_type'] ?? null;

        $query = CustomerPayment::query()
            ->with('customer')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc');

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($paymentType) {
            if ($paymentType === 'cash') {
                $query->whereNull('bank_name');
            } elseif ($paymentType === 'transfer') {
                $query->whereNotNull('bank_name');
            }
        }

        return $query->get()->map(function ($payment) {
            return [
                'payment_date' => $payment->payment_date->format('Y-m-d'),
                'customer_name' => $payment->customer->name,
                'amount_paid' => (float) $payment->amount,
                'payment_type' => $this->getPaymentType($payment->bank_name),
                'bank_name' => $payment->bank_name ?? 'N/A',
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        $totalPayments = $data->sum('amount_paid');
        $totalTransactions = $data->count();
        $cashPayments = $data->where('payment_type', 'Cash')->sum('amount_paid');
        $transferPayments = $data->where('payment_type', 'Transfer')->sum('amount_paid');

        return [
            'total_payments' => $totalPayments,
            'total_transactions' => $totalTransactions,
            'cash_payments' => $cashPayments,
            'transfer_payments' => $transferPayments,
            'average_payment' => $totalTransactions > 0 ? $totalPayments / $totalTransactions : 0,
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Group by payment type for pie chart
        $paymentTypeDistribution = $data->groupBy('payment_type')->map(function ($payments) {
            return [
                'count' => $payments->count(),
                'amount' => $payments->sum('amount_paid'),
            ];
        });

        // Group by month for line chart
        $monthlyData = $data->groupBy(function ($payment) {
            return \Carbon\Carbon::parse($payment['payment_date'])->format('Y-m');
        })->map(function ($payments) {
            return $payments->sum('amount_paid');
        })->sortKeys();

        return [
            'payment_types' => [
                'labels' => $paymentTypeDistribution->keys()->toArray(),
                'counts' => $paymentTypeDistribution->pluck('count')->toArray(),
                'amounts' => $paymentTypeDistribution->pluck('amount')->toArray(),
            ],
            'monthly_trends' => [
                'labels' => $monthlyData->keys()->toArray(),
                'amounts' => $monthlyData->values()->toArray(),
            ],
        ];
    }

    private function getPaymentType(?string $bankName): string
    {
        return $bankName ? 'Transfer' : 'Cash';
    }
}
