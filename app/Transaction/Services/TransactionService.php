<?php

declare(strict_types=1);

namespace App\Transaction\Services;

use App\Enums\NotificationType;
use App\Models\DailyCustomerTransaction;
use App\Notification\Services\NotificationService;
use App\Services\AtcAllocationValidator;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionService
{
    public function getPaginatedTransactions(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = DailyCustomerTransaction::with(['customer', 'driver', 'atc'])
            ->latest('date');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhere('cement_type', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('driver', function ($driverQuery) use ($search) {
                        $driverQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('status', $status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->get('driver_id'));
        }

        if ($request->filled('atc_id')) {
            $query->where('atc_id', $request->get('atc_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->get('date_to'));
        }

        if ($request->filled('cement_type')) {
            $query->where('cement_type', $request->get('cement_type'));
        }

        return $query->paginate($perPage);
    }

    public function createTransaction(array $data): DailyCustomerTransaction
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        // Validate ATC allocation before creating transaction
        $this->validateAtcAllocation($data);

        $transaction = DailyCustomerTransaction::create($data);

        AuditTrailService::log(
            'create',
            'Transaction',
            "Transaction for customer '{$transaction->customer->name}' was created with {$data['tons']} tons allocated from ATC #{$transaction->atc->atc_number}"
        );

        // Trigger notification for high-value transactions
        $this->triggerTransactionNotifications($transaction);

        return $transaction;
    }

    public function updateTransaction(DailyCustomerTransaction $transaction, array $data): DailyCustomerTransaction
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        // Validate ATC allocation before updating transaction
        $this->validateAtcAllocation($data, $transaction->id);

        $oldTons = $transaction->tons;
        $transaction->update($data);

        AuditTrailService::log(
            'update',
            'Transaction',
            "Transaction for customer '{$transaction->customer->name}' was updated. Tons changed from {$oldTons} to {$data['tons']} for ATC #{$transaction->atc->atc_number}"
        );

        return $transaction;
    }

    public function deleteTransaction(DailyCustomerTransaction $transaction): bool
    {
        $customerName = $transaction->customer->name;
        $result = $transaction->delete();

        AuditTrailService::log(
            'delete',
            'Transaction',
            "Transaction for customer '{$customerName}' was deleted"
        );

        return $result;
    }

    public function restoreTransaction(DailyCustomerTransaction $transaction): bool
    {
        $result = $transaction->restore();

        AuditTrailService::log(
            'restore',
            'Transaction',
            "Transaction for customer '{$transaction->customer->name}' was restored"
        );

        return $result;
    }

    public function forceDeleteTransaction(DailyCustomerTransaction $transaction): bool
    {
        $customerName = $transaction->customer->name;
        $result = $transaction->forceDelete();

        AuditTrailService::log(
            'force_delete',
            'Transaction',
            "Transaction for customer '{$customerName}' was permanently deleted"
        );

        return $result;
    }

    public function getTransactionStatistics(): array
    {
        $total = DailyCustomerTransaction::count();
        $active = DailyCustomerTransaction::active()->count();
        $inactive = DailyCustomerTransaction::inactive()->count();
        $recent = DailyCustomerTransaction::where('created_at', '>=', now()->subDays(30))->count();

        // Financial statistics
        $totalAtcCost = DailyCustomerTransaction::sum('atc_cost');
        $totalTransportCost = DailyCustomerTransaction::sum('transport_cost');
        $totalCost = $totalAtcCost + $totalTransportCost;

        // Today's transactions
        $todayTransactions = DailyCustomerTransaction::whereDate('date', today())->count();
        $todayRevenue = DailyCustomerTransaction::whereDate('date', today())->sum('atc_cost');

        // This month's transactions
        $monthTransactions = DailyCustomerTransaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        $monthRevenue = DailyCustomerTransaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('atc_cost');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'recent' => $recent,
            'total_atc_cost' => $totalAtcCost,
            'total_transport_cost' => $totalTransportCost,
            'total_cost' => $totalCost,
            'today_transactions' => $todayTransactions,
            'today_revenue' => $todayRevenue,
            'month_transactions' => $monthTransactions,
            'month_revenue' => $monthRevenue,
        ];
    }

    public function exportTransactions(Request $request): array
    {
        $query = DailyCustomerTransaction::with(['customer', 'driver', 'atc']);

        // Apply same filters as getPaginatedTransactions
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhere('cement_type', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('driver', function ($driverQuery) use ($search) {
                        $driverQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('status', $status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->get('driver_id'));
        }

        if ($request->filled('atc_id')) {
            $query->where('atc_id', $request->get('atc_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->get('date_to'));
        }

        if ($request->filled('cement_type')) {
            $query->where('cement_type', $request->get('cement_type'));
        }

        $transactions = $query->get();

        AuditTrailService::logDataExport('transactions', 'array', $transactions->count());

        return $transactions->toArray();
    }

    /**
     * Validate ATC allocation for a transaction
     */
    private function validateAtcAllocation(array $data, ?int $excludeTransactionId = null): void
    {
        if (! isset($data['atc_id']) || ! isset($data['tons'])) {
            return;
        }

        $atc = \App\Models\Atc::find($data['atc_id']);
        if (! $atc) {
            return;
        }

        $allocationValidator = app(AtcAllocationValidator::class);
        $remainingTons = $allocationValidator->getRemainingTons($atc, $excludeTransactionId);

        if ($data['tons'] > $remainingTons) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['tons' => ["The tons allocated ({$data['tons']}) exceeds the remaining capacity ({$remainingTons}) for ATC #{$atc->atc_number}."]]
            );
        }
    }

    /**
     * Get ATC allocation statistics for transactions
     */
    public function getAtcAllocationStatistics(): array
    {
        $allocationValidator = app(AtcAllocationValidator::class);

        // Get all ATCs with their allocation status
        $atcsWithAllocation = $allocationValidator->getAllAtcsWithAllocationStatus();

        $stats = [
            'total_atcs' => count($atcsWithAllocation),
            'available_atcs' => 0,
            'fully_allocated_atcs' => 0,
            'over_allocated_atcs' => 0,
            'total_tons' => 0,
            'allocated_tons' => 0,
            'remaining_tons' => 0,
            'total_transactions' => DailyCustomerTransaction::count(),
            'total_tons_allocated' => DailyCustomerTransaction::sum('tons'),
        ];

        foreach ($atcsWithAllocation as $atcData) {
            $allocation = $atcData['allocation'];

            $stats['total_tons'] += $allocation['total_tons'];
            $stats['allocated_tons'] += $allocation['allocated_tons'];
            $stats['remaining_tons'] += $allocation['remaining_tons'];

            if ($allocation['is_over_allocated']) {
                $stats['over_allocated_atcs']++;
            } elseif ($allocation['is_fully_allocated']) {
                $stats['fully_allocated_atcs']++;
            } else {
                $stats['available_atcs']++;
            }
        }

        return $stats;
    }

    /**
     * Get transactions with ATC allocation information
     */
    public function getTransactionsWithAllocationInfo(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $transactions = $this->getPaginatedTransactions($request, $perPage);

        // Add allocation info to each transaction
        $allocationValidator = app(AtcAllocationValidator::class);

        $transactions->getCollection()->transform(function (DailyCustomerTransaction $transaction) use ($allocationValidator) {
            $transaction->atc_allocation = $allocationValidator->getAllocationSummary($transaction->atc);

            return $transaction;
        });

        return $transactions;
    }

    /**
     * Trigger notifications for transaction events
     */
    private function triggerTransactionNotifications(DailyCustomerTransaction $transaction): void
    {
        $notificationService = app(NotificationService::class);

        // High-value transaction alert (>₦500,000)
        $atcCost = (float) $transaction->atc_cost;
        if ($atcCost > 500000) {
            $notificationService->createSystemNotification(
                NotificationType::TRANSACTION_ALERT,
                'High-Value Transaction Alert',
                'High-value transaction created: ₦'.number_format($atcCost, 2)." for customer {$transaction->customer->name}.",
                [
                    'transaction_id' => $transaction->id,
                    'customer_id' => $transaction->customer->id,
                    'customer_name' => $transaction->customer->name,
                    'atc_id' => $transaction->atc->id,
                    'atc_number' => $transaction->atc->atc_number,
                    'amount' => $atcCost,
                    'tons' => $transaction->tons,
                ],
                now()->addDays(3) // Expire in 3 days
            );
        }
    }
}
