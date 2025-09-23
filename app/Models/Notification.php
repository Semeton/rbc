<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationPriority;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'priority',
        'title',
        'message',
        'data',
        'user_id',
        'read_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only unread notifications
     */
    public function scopeUnread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    /**
     * Scope to get only read notifications
     */
    public function scopeRead(Builder $query): void
    {
        $query->whereNotNull('read_at');
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    /**
     * Scope to filter by priority
     */
    public function scopeByPriority(Builder $query, string $priority): void
    {
        $query->where('priority', $priority);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope to get system-wide notifications (no user_id)
     */
    public function scopeSystemWide(Builder $query): void
    {
        $query->whereNull('user_id');
    }

    /**
     * Scope to get non-expired notifications
     */
    public function scopeNotExpired(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to get expired notifications
     */
    public function scopeExpired(Builder $query): void
    {
        $query->where('expires_at', '<=', now());
    }

    /**
     * Scope to search notifications
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        return $this->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Check if notification is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the notification type enum
     */
    public function getTypeEnum(): NotificationType
    {
        return NotificationType::from($this->type);
    }

    /**
     * Get the priority enum
     */
    public function getPriorityEnum(): NotificationPriority
    {
        return NotificationPriority::from($this->priority);
    }

    /**
     * Get the formatted priority display
     */
    public function getPriorityDisplayAttribute(): string
    {
        return $this->getPriorityEnum()->getDisplayName();
    }

    /**
     * Get the priority color class
     */
    public function getPriorityColorClassAttribute(): string
    {
        return $this->getPriorityEnum()->getColorClass();
    }

    /**
     * Get the priority background color class
     */
    public function getPriorityBackgroundColorClassAttribute(): string
    {
        return $this->getPriorityEnum()->getBackgroundColorClass();
    }

    /**
     * Get the type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return $this->getTypeEnum()->getDisplayName();
    }

    /**
     * Get the time since notification was created
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
