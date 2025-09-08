<?php

namespace App\Models;

use Database\Factories\AuditTrailFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrail extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return AuditTrailFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction(Builder $query, string $action): void
    {
        $query->where('action', $action);
    }

    /**
     * Scope to filter by module
     */
    public function scopeByModule(Builder $query, string $module): void
    {
        $query->where('module', $module);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent audit trails
     */
    public function scopeRecent(Builder $query, int $days = 30): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get the user's name or 'System' if no user
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'System';
    }

    /**
     * Get the formatted action
     */
    public function getFormattedActionAttribute(): string
    {
        return ucfirst($this->action);
    }

    /**
     * Get the formatted module
     */
    public function getFormattedModuleAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->module));
    }
}
