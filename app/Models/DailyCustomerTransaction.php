<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DailyCustomerTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'datetime',
        'atc_cost' => 'decimal:2',
        'transport_cost' => 'decimal:2',
        'tons' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Get the customer for this transaction
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the driver for this transaction
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the ATC for this transaction
     */
    public function atc(): BelongsTo
    {
        return $this->belongsTo(Atc::class);
    }

    /**
     * Scope to get only active transactions
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }

    /**
     * Scope to get only inactive transactions
     */
    public function scopeInactive(Builder $query): void
    {
        $query->where('status', false);
    }

    /**
     * Get the status as a string
     */
    public function getStatusStringAttribute(): string
    {
        return $this->status ? 'active' : 'inactive';
    }

    /**
     * Set the status from a string
     */
    public function setStatusStringAttribute(string $value): void
    {
        $this->attributes['status'] = $value === 'active';
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by customer
     */
    public function scopeByCustomer(Builder $query, int $customerId): void
    {
        $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter by driver
     */
    public function scopeByDriver(Builder $query, int $driverId): void
    {
        $query->where('driver_id', $driverId);
    }

    /**
     * Scope to filter by ATC
     */
    public function scopeByAtc(Builder $query, int $atcId): void
    {
        $query->where('atc_id', $atcId);
    }

    /**
     * Get the total cost for this transaction
     */
    public function getTotalCostAttribute(): float
    {
        return $this->atc_cost + $this->transport_cost;
    }

    /**
     * Get the transaction status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($transaction) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'module' => 'Transaction',
                'description' => "Transaction for customer '{$transaction->customer->name}' was created",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::updated(function ($transaction) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'module' => 'Transaction',
                'description' => "Transaction for customer '{$transaction->customer->name}' was updated",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::deleted(function ($transaction) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'module' => 'Transaction',
                'description' => "Transaction for customer '{$transaction->customer->name}' was deleted",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
