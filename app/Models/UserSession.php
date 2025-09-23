<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    /**
     * Get the user that owns the session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('last_activity', '>', now()->subHours(24));
    }

    /**
     * Scope to get only expired sessions
     */
    public function scopeExpired($query)
    {
        return $query->where('last_activity', '<=', now()->subHours(24));
    }
}
