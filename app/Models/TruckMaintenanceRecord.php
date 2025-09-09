<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TruckMaintenanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'cost_of_maintenance' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Get the truck for this maintenance record
     */
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * Scope to get only active maintenance records
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }

    /**
     * Scope to filter by truck
     */
    public function scopeByTruck(Builder $query, int $truckId): void
    {
        $query->where('truck_id', $truckId);
    }

    /**
     * Scope to filter by cost range
     */
    public function scopeByCostRange(Builder $query, float $minCost, float $maxCost): void
    {
        $query->whereBetween('cost_of_maintenance', [$minCost, $maxCost]);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the formatted cost
     */
    public function getFormattedCostAttribute(): string
    {
        return number_format($this->cost_of_maintenance, 2);
    }

    /**
     * Get the maintenance status display name
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
     * Scope to get only inactive maintenance records
     */
    public function scopeInactive(Builder $query): void
    {
        $query->where('status', false);
    }

    /**
     * Scope to get recent maintenance records
     */
    public function scopeRecent(Builder $query, int $days = 30): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to search maintenance records
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('truck', function ($truckQuery) use ($search) {
                    $truckQuery->where('registration_number', 'like', "%{$search}%")
                        ->orWhere('cab_number', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($maintenance) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'module' => 'Maintenance',
                'description' => "Maintenance record for truck '{$maintenance->truck->registration_number}' was created",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::updated(function ($maintenance) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'module' => 'Maintenance',
                'description' => "Maintenance record for truck '{$maintenance->truck->registration_number}' was updated",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::deleted(function ($maintenance) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'module' => 'Maintenance',
                'description' => "Maintenance record for truck '{$maintenance->truck->registration_number}' was deleted",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
