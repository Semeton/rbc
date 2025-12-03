<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DailyTruckRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'truck_id',
        'customer_id',
        'atc_id',
        'atc_collection_date',
        'load_dispatch_date',
        'customer_cost',
        'fare',
        'gas_chop_money',
        'haulage',
        'incentive',
        'salary_contribution',
        'balance',
        'status',
    ];

    protected $casts = [
        'atc_collection_date' => 'datetime',
        'load_dispatch_date' => 'datetime',
        'customer_cost' => 'decimal:2',
        'fare' => 'decimal:2',
        'gas_chop_money' => 'decimal:2',
        'haulage' => 'decimal:2',
        'incentive' => 'decimal:2',
        'salary_contribution' => 'decimal:2',
        'balance' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Get the driver for this record
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the truck for this record
     */
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * Get the customer for this record
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope to get only active records
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('atc_collection_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by driver
     */
    public function scopeByDriver(Builder $query, int $driverId): void
    {
        $query->where('driver_id', $driverId);
    }

    /**
     * Scope to filter by truck
     */
    public function scopeByTruck(Builder $query, int $truckId): void
    {
        $query->where('truck_id', $truckId);
    }

    /**
     * Scope to filter by customer
     */
    public function scopeByCustomer(Builder $query, int $customerId): void
    {
        $query->where('customer_id', $customerId);
    }

    /**
     * Get the ATC for this record
     */
    public function atc(): BelongsTo
    {
        return $this->belongsTo(Atc::class);
    }

    /**
     * Get the net profit for this record
     */
    public function getNetProfitAttribute(): float
    {
        return (float) $this->fare - (float) $this->gas_chop_money;
    }

    /**
     * Get the per-movement total (Fare - Gas + Haulage).
     */
    public function getTotalAttribute(): float
    {
        $haulage = $this->haulage ?? 0;

        return (float) $this->fare - (float) $this->gas_chop_money + (float) $haulage;
    }

    /**
     * Get the per-movement total including incentive (Total + Incentive).
     */
    public function getTotalPlusIncentiveAttribute(): float
    {
        $incentive = $this->incentive ?? 0;

        return $this->total + (float) $incentive;
    }

    /**
     * Get the record status display name
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
     * Scope to get only inactive records
     */
    public function scopeInactive(Builder $query): void
    {
        $query->where('status', false);
    }

    /**
     * Scope to get recent records
     */
    public function scopeRecent(Builder $query, int $days = 30): void
    {
        $query->where('atc_collection_date', '>=', now()->subDays($days));
    }

    /**
     * Scope to search records
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('driver', function ($driverQuery) use ($search) {
                $driverQuery->where('name', 'like', "%{$search}%");
            })
                ->orWhereHas('truck', function ($truckQuery) use ($search) {
                    $truckQuery->where('registration_number', 'like', "%{$search}%")
                        ->orWhere('cab_number', 'like', "%{$search}%");
                })
                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($record) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'module' => 'Truck Record',
                'description' => "Truck record for '{$record->truck->registration_number}' was created",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::updated(function ($record) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'module' => 'Truck Record',
                'description' => "Truck record for '{$record->truck->registration_number}' was updated",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::deleted(function ($record) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'module' => 'Truck Record',
                'description' => "Truck record for '{$record->truck->registration_number}' was deleted",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
