<?php

declare(strict_types=1);

namespace App\Transaction\Services;

use App\Models\DailyCustomerTransaction;
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

        $transaction = DailyCustomerTransaction::create($data);

        AuditTrailService::log(
            'create',
            'Transaction',
            "Transaction for customer '{$transaction->customer->name}' was created"
        );

        return $transaction;
    }

    public function updateTransaction(DailyCustomerTransaction $transaction, array $data): DailyCustomerTransaction
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        $transaction->update($data);

        AuditTrailService::log(
            'update',
            'Transaction',
            "Transaction for customer '{$transaction->customer->name}' was updated"
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
}
