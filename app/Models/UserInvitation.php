<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UserRolesAndPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class UserInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'role',
        'token',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Get the user who sent the invitation
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Check if the invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the invitation is accepted
     */
    public function isAccepted(): bool
    {
        return !is_null($this->accepted_at);
    }

    /**
     * Check if the invitation is pending
     */
    public function isPending(): bool
    {
        return !$this->isAccepted() && !$this->isExpired();
    }

    /**
     * Get the role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return UserRolesAndPermission::roles($this->role);
    }

    /**
     * Scope to get only pending invitations
     */
    public function scopePending($query)
    {
        return $query->whereNull('accepted_at')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope to get only expired invitations
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope to get only accepted invitations
     */
    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }
}
