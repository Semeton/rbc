<?php

declare(strict_types=1);

namespace App\Payment\Services;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService
{
    /**
     * Get paginated payments with filters
     */
    public function getPaginatedPayments(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = CustomerPayment::with('customer')
            ->latest('payment_date');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('customer_id')) {
            $query->byCustomer((int) $request->customer_id);
        }

        if ($request->filled('bank_name')) {
            $query->byBank($request->bank_name);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('amount_min') && $request->filled('amount_max')) {
            $query->byAmountRange((float) $request->amount_min, (float) $request->amount_max);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new payment
     */
    public function createPayment(array $data): CustomerPayment
    {
        $payment = CustomerPayment::create($data);

        // Log audit trail
        AuditTrailService::log('create', 'Payment', "Payment of {$payment->amount} for customer '{$payment->customer->name}' was created");

        return $payment;
    }

    /**
     * Update an existing payment
     */
    public function updatePayment(CustomerPayment $payment, array $data): CustomerPayment
    {
        $payment->update($data);

        // Log audit trail
        AuditTrailService::log('update', 'Payment', "Payment of {$payment->amount} for customer '{$payment->customer->name}' was updated");

        return $payment;
    }

    /**
     * Delete a payment (soft delete)
     */
    public function deletePayment(CustomerPayment $payment): bool
    {
        $customerName = $payment->customer->name;
        $amount = $payment->amount;

        $result = $payment->delete();

        // Log audit trail
        AuditTrailService::log('delete', 'Payment', "Payment of {$amount} for customer '{$customerName}' was deleted");

        return $result;
    }

    /**
     * Restore a soft-deleted payment
     */
    public function restorePayment(CustomerPayment $payment): bool
    {
        $result = $payment->restore();

        // Log audit trail
        AuditTrailService::log('restore', 'Payment', "Payment of {$payment->amount} for customer '{$payment->customer->name}' was restored");

        return $result;
    }

    /**
     * Permanently delete a payment
     */
    public function forceDeletePayment(CustomerPayment $payment): bool
    {
        $customerName = $payment->customer->name;
        $amount = $payment->amount;

        $result = $payment->forceDelete();

        // Log audit trail
        AuditTrailService::log('force_delete', 'Payment', "Payment of {$amount} for customer '{$customerName}' was permanently deleted");

        return $result;
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics(): array
    {
        $totalPayments = CustomerPayment::count();
        $totalAmount = CustomerPayment::sum('amount');
        $recentPayments = CustomerPayment::recent(30)->count();
        $averageAmount = $totalPayments > 0 ? $totalAmount / $totalPayments : 0;

        return [
            'total' => $totalPayments,
            'total_amount' => $totalAmount,
            'recent' => $recentPayments,
            'average_amount' => $averageAmount,
        ];
    }

    /**
     * Export payments data
     */
    public function exportPayments(Request $request): Collection
    {
        $query = CustomerPayment::with('customer');

        // Apply same filters as pagination
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('customer_id')) {
            $query->byCustomer((int) $request->customer_id);
        }

        if ($request->filled('bank_name')) {
            $query->byBank($request->bank_name);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('amount_min') && $request->filled('amount_max')) {
            $query->byAmountRange((float) $request->amount_min, (float) $request->amount_max);
        }

        $payments = $query->get();

        // Log audit trail
        AuditTrailService::log('export', 'Payment', "Payments data exported ({$payments->count()} records)");

        return $payments;
    }

    /**
     * Get customer balance (total payments - total transactions)
     */
    public function getCustomerBalance(Customer $customer): float
    {
        $totalPayments = CustomerPayment::where('customer_id', $customer->id)->sum('amount');
        $totalTransactions = $customer->transactions()->sum('atc_cost') + $customer->transactions()->sum('transport_cost');

        return $totalPayments - $totalTransactions;
    }

    /**
     * Get payments for a specific customer
     */
    public function getCustomerPayments(Customer $customer, int $perPage = 15): LengthAwarePaginator
    {
        return CustomerPayment::where('customer_id', $customer->id)
            ->with('customer')
            ->latest('payment_date')
            ->paginate($perPage);
    }
}
