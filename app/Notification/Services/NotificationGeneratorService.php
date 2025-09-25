<?php

declare(strict_types=1);

namespace App\Notification\Services;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use App\Models\Atc;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\TruckMaintenanceRecord;
use App\Models\User;
use App\Notification\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationGeneratorService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Generate all pending ATC notifications
     */
    public function generatePendingAtcNotifications(): int
    {
        $count = 0;
        
        // Find ATCs that have been unassigned for more than 3 days
        $unassignedAtcs = Atc::whereDoesntHave('transactions')
            ->where('created_at', '<', now()->subDays(3))
            ->get();

        foreach ($unassignedAtcs as $atc) {
            $this->notificationService->createSystemNotification(
                NotificationType::PENDING_ATC,
                "Pending ATC Assignment",
                "ATC #{$atc->atc_number} has been unassigned for more than 3 days. Please assign it to a customer.",
                [
                    'atc_id' => $atc->id,
                    'atc_number' => $atc->atc_number,
                    'company' => $atc->company,
                    'days_unassigned' => $atc->created_at->diffInDays(now()),
                ],
                now()->addDays(7) // Expire in 7 days
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate overdue balance notifications
     */
    public function generateOverdueBalanceNotifications(): int
    {
        $count = 0;
        
        // Find customers with outstanding balances over ₦100,000 for more than 30 days
        $customersWithOverdueBalances = Customer::whereHas('transactions', function ($query) {
            $query->where('created_at', '<', now()->subDays(30));
        })
        ->with(['transactions' => function ($query) {
            $query->where('created_at', '<', now()->subDays(30));
        }])
        ->get()
        ->filter(function ($customer) {
            $totalTransactions = $customer->transactions->sum('atc_cost');
            $totalPayments = $customer->payments->sum('amount');
            $outstandingBalance = $totalTransactions - $totalPayments;
            
            return $outstandingBalance > 100000; // ₦100,000
        });

        foreach ($customersWithOverdueBalances as $customer) {
            $totalTransactions = $customer->transactions->sum('atc_cost');
            $totalPayments = $customer->payments->sum('amount');
            $outstandingBalance = $totalTransactions - $totalPayments;

            $this->notificationService->createSystemNotification(
                NotificationType::OVERDUE_BALANCE,
                "Overdue Customer Balance",
                "Customer {$customer->name} has an overdue balance of ₦" . number_format($outstandingBalance, 2) . " for more than 30 days.",
                [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'outstanding_balance' => $outstandingBalance,
                    'days_overdue' => $customer->transactions->min('created_at')->diffInDays(now()),
                ],
                now()->addDays(14) // Expire in 14 days
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate maintenance reminder notifications
     */
    public function generateMaintenanceReminderNotifications(): int
    {
        $count = 0;
        
        // Find trucks with maintenance records created in the last 7 days
        // (Since there's no scheduled_date, we'll use created_at as a proxy)
        $recentMaintenance = TruckMaintenanceRecord::where('created_at', '>=', now()->subDays(7))
            ->where('status', false) // Not completed
            ->with('truck')
            ->get();

        foreach ($recentMaintenance as $maintenance) {
            $daysSinceMaintenance = now()->diffInDays($maintenance->created_at);
            
            $this->notificationService->createSystemNotification(
                NotificationType::MAINTENANCE_REMINDER,
                "Truck Maintenance Record",
                "Truck {$maintenance->truck->truck_number} has a maintenance record from {$maintenance->created_at->format('M d, Y')}.",
                [
                    'maintenance_id' => $maintenance->id,
                    'truck_id' => $maintenance->truck->id,
                    'truck_number' => $maintenance->truck->truck_number,
                    'maintenance_date' => $maintenance->created_at->toDateString(),
                    'maintenance_type' => $maintenance->description,
                    'days_since' => $daysSinceMaintenance,
                ],
                now()->addDays(7) // Expire in 7 days
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate transaction alert notifications
     */
    public function generateTransactionAlertNotifications(): int
    {
        $count = 0;
        
        // Find high-value transactions (>₦500,000) created in the last hour
        $highValueTransactions = DailyCustomerTransaction::where('atc_cost', '>', 500000)
            ->where('created_at', '>=', now()->subHour())
            ->with(['customer', 'atc'])
            ->get();

        foreach ($highValueTransactions as $transaction) {
            $this->notificationService->createSystemNotification(
                NotificationType::TRANSACTION_ALERT,
                "High-Value Transaction Alert",
                "High-value transaction created: ₦" . number_format($transaction->atc_cost, 2) . " for customer {$transaction->customer->name}.",
                [
                    'transaction_id' => $transaction->id,
                    'customer_id' => $transaction->customer->id,
                    'customer_name' => $transaction->customer->name,
                    'atc_id' => $transaction->atc->id,
                    'atc_number' => $transaction->atc->atc_number,
                    'amount' => $transaction->atc_cost,
                    'tons' => $transaction->tons,
                ],
                now()->addDays(3) // Expire in 3 days
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate payment reminder notifications
     */
    public function generatePaymentReminderNotifications(): int
    {
        $count = 0;
        
        // Find customers who haven't made payments for more than 45 days
        $customersWithoutRecentPayments = Customer::whereDoesntHave('payments', function ($query) {
            $query->where('payment_date', '>=', now()->subDays(45));
        })
        ->whereHas('transactions', function ($query) {
            $query->where('created_at', '<', now()->subDays(45));
        })
        ->with(['transactions', 'payments'])
        ->get();

        foreach ($customersWithoutRecentPayments as $customer) {
            $lastPayment = $customer->payments->sortByDesc('payment_date')->first();
            $daysSinceLastPayment = $lastPayment ? $lastPayment->payment_date->diffInDays(now()) : 45;

            $this->notificationService->createSystemNotification(
                NotificationType::PAYMENT_REMINDER,
                "Payment Reminder",
                "Customer {$customer->name} has not made a payment for {$daysSinceLastPayment} days. Please follow up.",
                [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'days_since_payment' => $daysSinceLastPayment,
                    'last_payment_date' => $lastPayment?->payment_date?->toDateString(),
                ],
                now()->addDays(7) // Expire in 7 days
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate system alert notifications
     */
    public function generateSystemAlertNotifications(): int
    {
        $count = 0;
        
        // Check for system issues or maintenance windows
        // This is a placeholder for system-specific alerts
        
        // Example: Check for expired notifications that need cleanup
        $expiredNotificationsCount = \App\Models\Notification::expired()->count();
        
        if ($expiredNotificationsCount > 100) {
            $this->notificationService->createSystemNotification(
                NotificationType::SYSTEM_ALERT,
                "System Maintenance Required",
                "There are {$expiredNotificationsCount} expired notifications that need cleanup. Consider running the cleanup process.",
                [
                    'expired_count' => $expiredNotificationsCount,
                    'cleanup_required' => true,
                ],
                now()->addDays(1) // Expire in 1 day
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate all notifications
     */
    public function generateAllNotifications(): array
    {
        return [
            'pending_atc' => $this->generatePendingAtcNotifications(),
            'overdue_balance' => $this->generateOverdueBalanceNotifications(),
            'maintenance_reminder' => $this->generateMaintenanceReminderNotifications(),
            'transaction_alert' => $this->generateTransactionAlertNotifications(),
            'payment_reminder' => $this->generatePaymentReminderNotifications(),
            'system_alert' => $this->generateSystemAlertNotifications(),
        ];
    }

    /**
     * Generate notifications for a specific type
     */
    public function generateNotificationsByType(NotificationType $type): int
    {
        return match ($type) {
            NotificationType::PENDING_ATC => $this->generatePendingAtcNotifications(),
            NotificationType::OVERDUE_BALANCE => $this->generateOverdueBalanceNotifications(),
            NotificationType::MAINTENANCE_REMINDER => $this->generateMaintenanceReminderNotifications(),
            NotificationType::TRANSACTION_ALERT => $this->generateTransactionAlertNotifications(),
            NotificationType::PAYMENT_REMINDER => $this->generatePaymentReminderNotifications(),
            NotificationType::SYSTEM_ALERT => $this->generateSystemAlertNotifications(),
        };
    }
}
