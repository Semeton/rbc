<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    /**
     * Get the display name for the priority
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::CRITICAL => 'Critical',
        };
    }

    /**
     * Get the color class for the priority
     */
    public function getColorClass(): string
    {
        return match ($this) {
            self::LOW => 'text-gray-600',
            self::MEDIUM => 'text-blue-600',
            self::HIGH => 'text-orange-600',
            self::CRITICAL => 'text-red-600',
        };
    }

    /**
     * Get the background color class for the priority
     */
    public function getBackgroundColorClass(): string
    {
        return match ($this) {
            self::LOW => 'bg-gray-100',
            self::MEDIUM => 'bg-blue-100',
            self::HIGH => 'bg-orange-100',
            self::CRITICAL => 'bg-red-100',
        };
    }

    /**
     * Get all priorities as array
     */
    public static function getAllPriorities(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
