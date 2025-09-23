<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationType: string
{
    case PENDING_ATC = 'pending_atc';
    case OVERDUE_BALANCE = 'overdue_balance';
    case MAINTENANCE_REMINDER = 'maintenance_reminder';
    case TRANSACTION_ALERT = 'transaction_alert';
    case PAYMENT_REMINDER = 'payment_reminder';
    case SYSTEM_ALERT = 'system_alert';

    /**
     * Get the display name for the notification type
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::PENDING_ATC => 'Pending ATC',
            self::OVERDUE_BALANCE => 'Overdue Balance',
            self::MAINTENANCE_REMINDER => 'Maintenance Reminder',
            self::TRANSACTION_ALERT => 'Transaction Alert',
            self::PAYMENT_REMINDER => 'Payment Reminder',
            self::SYSTEM_ALERT => 'System Alert',
        };
    }

    /**
     * Get the default priority for the notification type
     */
    public function getDefaultPriority(): string
    {
        return match ($this) {
            self::PENDING_ATC => 'medium',
            self::OVERDUE_BALANCE => 'high',
            self::MAINTENANCE_REMINDER => 'medium',
            self::TRANSACTION_ALERT => 'high',
            self::PAYMENT_REMINDER => 'critical',
            self::SYSTEM_ALERT => 'critical',
        };
    }

    /**
     * Get all notification types as array
     */
    public static function getAllTypes(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
