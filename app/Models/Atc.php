<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Atc extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get all transactions using this ATC
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(DailyCustomerTransaction::class);
    }

    /**
     * Scope to get only active ATCs
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }

    /**
     * Scope to filter by ATC type
     */
    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('atc_type', $type);
    }

    /**
     * Scope to search ATCs by number
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where('atc_number', 'like', "%{$search}%");
    }

    /**
     * Get the ATC type display name
     */
    public function getAtcTypeAttribute(): string
    {
        return match ($this->attributes['atc_type']) {
            'bg' => 'BG',
            'cash_payment' => 'Cash Payment',
            default => $this->attributes['atc_type'],
        };
    }

    /**
     * Get the ATC status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Get the status as a string for UI display
     */
    public function getStatusStringAttribute(): string
    {
        return $this->status ? 'active' : 'inactive';
    }

    /**
     * Set the status from a string value
     */
    public function setStatusStringAttribute(string $value): void
    {
        $this->attributes['status'] = $value === 'active';
    }

    /**
     * Get the total tons allocated to transactions
     */
    public function getAllocatedTonsAttribute(): float
    {
        return $this->transactions()->sum('tons') ?? 0;
    }

    /**
     * Get the remaining tons available for allocation
     */
    public function getRemainingTonsAttribute(): float
    {
        return $this->tons - $this->allocated_tons;
    }

    /**
     * Check if the ATC is fully allocated
     */
    public function getIsFullyAllocatedAttribute(): bool
    {
        return $this->remaining_tons <= 0;
    }

    /**
     * Check if the ATC is over-allocated
     */
    public function getIsOverAllocatedAttribute(): bool
    {
        return $this->allocated_tons > $this->tons;
    }

    /**
     * Get allocation percentage
     */
    public function getAllocationPercentageAttribute(): float
    {
        if ($this->tons == 0) {
            return 0;
        }
        
        return ($this->allocated_tons / $this->tons) * 100;
    }

    /**
     * Get remaining amount based on allocation
     */
    public function getRemainingAmountAttribute(): float
    {
        $allocatedAmount = $this->transactions()
            ->where('status', true)
            ->sum('atc_cost');
        
        return $this->amount - $allocatedAmount;
    }

    /**
     * Get price per ton
     */
    public function getPricePerTonAttribute(): float
    {
        if ($this->tons == 0) {
            return 0;
        }
        
        return $this->amount / $this->tons;
    }

    /**
     * Scope to get only inactive ATCs
     */
    public function scopeInactive(Builder $query): void
    {
        $query->where('status', false);
    }

    /**
     * Check if ATC is available for use
     */
    public function isAvailable(): bool
    {
        return $this->status && $this->transactions()->where('status', true)->count() === 0;
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($atc) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'module' => 'ATC',
                'description' => "ATC '{$atc->atc_number}' was created",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::updated(function ($atc) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'module' => 'ATC',
                'description' => "ATC '{$atc->atc_number}' was updated",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::deleted(function ($atc) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'module' => 'ATC',
                'description' => "ATC '{$atc->atc_number}' was deleted",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
