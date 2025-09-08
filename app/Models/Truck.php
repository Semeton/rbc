<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Truck extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
        'year_of_manufacture' => 'integer',
    ];

    /**
     * Get all maintenance records for this truck
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(TruckMaintenanceRecord::class);
    }

    /**
     * Get all truck records for this truck
     */
    public function truckRecords(): HasMany
    {
        return $this->hasMany(DailyTruckRecord::class);
    }

    /**
     * Scope to get only active trucks
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }

    /**
     * Scope to get only inactive trucks
     */
    public function scopeInactive(Builder $query): void
    {
        $query->where('status', false);
    }

    /**
     * Scope to search trucks by registration number or model
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('registration_number', 'like', "%{$search}%")
                ->orWhere('cab_number', 'like', "%{$search}%")
                ->orWhere('truck_model', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter trucks by year range
     */
    public function scopeByYearRange(Builder $query, int $startYear, int $endYear): void
    {
        $query->whereBetween('year_of_manufacture', [$startYear, $endYear]);
    }

    /**
     * Get the truck's age in years
     */
    public function getAgeAttribute(): int
    {
        return now()->year - $this->year_of_manufacture;
    }

    /**
     * Get total maintenance cost for this truck
     */
    public function getTotalMaintenanceCostAttribute(): float
    {
        return $this->maintenanceRecords()->sum('cost_of_maintenance');
    }

    /**
     * Get the truck's display name
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->truck_model} ({$this->registration_number})";
    }

    /**
     * Get the status as a string for display purposes
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
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($truck) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'module' => 'Truck',
                'description' => "Truck '{$truck->registration_number}' was created",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::updated(function ($truck) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'module' => 'Truck',
                'description' => "Truck '{$truck->registration_number}' was updated",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::deleted(function ($truck) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'module' => 'Truck',
                'description' => "Truck '{$truck->registration_number}' was deleted",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
