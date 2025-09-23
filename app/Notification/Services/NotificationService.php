<?php

declare(strict_types=1);

namespace App\Notification\Services;

use App\Models\Notification;
use App\Models\User;
use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class NotificationService
{
    public function __construct(
        private readonly AuditTrailService $auditTrailService
    ) {}

    /**
     * Create a new notification
     */
    public function createNotification(array $data): Notification
    {
        $notification = Notification::create($data);

        $this->auditTrailService->log(
            'create',
            'Notification',
            "Notification '{$notification->title}' was created"
        );

        return $notification;
    }

    /**
     * Create a system-wide notification
     */
    public function createSystemNotification(
        NotificationType $type,
        string $title,
        string $message,
        array $data = [],
        ?Carbon $expiresAt = null
    ): Notification {
        $priority = NotificationPriority::from($type->getDefaultPriority());

        return $this->createNotification([
            'type' => $type->value,
            'priority' => $priority->value,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'user_id' => null, // System-wide
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Create a user-specific notification
     */
    public function createUserNotification(
        User $user,
        NotificationType $type,
        string $title,
        string $message,
        array $data = [],
        ?Carbon $expiresAt = null
    ): Notification {
        $priority = NotificationPriority::from($type->getDefaultPriority());

        return $this->createNotification([
            'type' => $type->value,
            'priority' => $priority->value,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'user_id' => $user->id,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Get paginated notifications with filters
     */
    public function getPaginatedNotifications(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Notification::with(['user'])
            ->notExpired()
            ->latest('created_at');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('user_id')) {
            $query->forUser((int) $request->user_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->read();
            } elseif ($request->status === 'unread') {
                $query->unread();
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Get notifications for a specific user
     */
    public function getUserNotifications(
        User $user,
        int $limit = 10,
        bool $unreadOnly = false
    ): Collection {
        $query = Notification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereNull('user_id'); // System-wide notifications
        })
        ->notExpired()
        ->latest('created_at');

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereNull('user_id'); // System-wide notifications
        })
        ->unread()
        ->notExpired()
        ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): bool
    {
        if ($notification->isRead()) {
            return true;
        }

        $result = $notification->markAsRead();

        if ($result) {
            $this->auditTrailService->log(
                'update',
                'Notification',
                "Notification '{$notification->title}' was marked as read"
            );
        }

        return $result;
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Notification $notification): bool
    {
        if ($notification->isUnread()) {
            return true;
        }

        $result = $notification->markAsUnread();

        if ($result) {
            $this->auditTrailService->log(
                'update',
                'Notification',
                "Notification '{$notification->title}' was marked as unread"
            );
        }

        return $result;
    }

    /**
     * Mark multiple notifications as read
     */
    public function markMultipleAsRead(array $notificationIds): int
    {
        $count = Notification::whereIn('id', $notificationIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($count > 0) {
            $this->auditTrailService->log(
                'update',
                'Notification',
                "{$count} notifications were marked as read"
            );
        }

        return $count;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsReadForUser(User $user): int
    {
        $count = Notification::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereNull('user_id'); // System-wide notifications
        })
        ->unread()
        ->update(['read_at' => now()]);

        if ($count > 0) {
            $this->auditTrailService->log(
                'update',
                'Notification',
                "All notifications were marked as read for user {$user->name}"
            );
        }

        return $count;
    }

    /**
     * Delete a notification
     */
    public function deleteNotification(Notification $notification): bool
    {
        $title = $notification->title;
        $result = $notification->delete();

        if ($result) {
            $this->auditTrailService->log(
                'delete',
                'Notification',
                "Notification '{$title}' was deleted"
            );
        }

        return $result;
    }

    /**
     * Clean up expired notifications
     */
    public function cleanupExpiredNotifications(): int
    {
        $count = Notification::expired()->count();
        
        if ($count > 0) {
            Notification::expired()->delete();
            
            $this->auditTrailService->log(
                'delete',
                'Notification',
                "{$count} expired notifications were cleaned up"
            );
        }

        return $count;
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats(): array
    {
        return [
            'total' => Notification::count(),
            'unread' => Notification::unread()->count(),
            'read' => Notification::read()->count(),
            'expired' => Notification::expired()->count(),
            'by_type' => Notification::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_priority' => Notification::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
        ];
    }
}
