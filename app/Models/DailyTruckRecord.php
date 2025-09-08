<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DailyTruckRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'truck_id',
        'customer_id',
        'atc_collection_date',
        'load_dispatch_date',
        'fare',
        'gas_chop_money',
        'balance',
        'status',
    ];

    protected $casts = [
        'atc_collection_date' => 'datetime',
        'load_dispatch_date' => 'datetime',
        'fare' => 'decimal:2',
        'gas_chop_money' => 'decimal:2',
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
     * Get the net profit for this record
     */
    public function getNetProfitAttribute(): float
    {
        return $this->fare - $this->gas_chop_money;
    }

    /**
     * Get the record status display name
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
