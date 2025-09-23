<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\UserRolesAndPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, UserRolesAndPermission;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function role(): string
    {
        return $this->roles($this->attributes['role']);
    }

    public function permission(): array
    {
        return $this->permissions($this->attributes['role']);
    }

    /**
     * Get the user's invitations
     */
    public function invitations()
    {
        return $this->hasMany(UserInvitation::class, 'invited_by');
    }

    /**
     * Get the user's sessions
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the user's preferences
     */
    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Get the user's status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            default => 'Unknown',
        };
    }

    /**
     * Get the user's status color class
     */
    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'text-green-600',
            'inactive' => 'text-gray-600',
            'suspended' => 'text-red-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Get the user's status background color class
     */
    public function getStatusBackgroundColorClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-green-100',
            'inactive' => 'bg-gray-100',
            'suspended' => 'bg-red-100',
            default => 'bg-gray-100',
        };
    }
}
