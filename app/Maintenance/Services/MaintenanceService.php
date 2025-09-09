<?php

declare(strict_types=1);

namespace App\Maintenance\Services;

use App\Models\TruckMaintenanceRecord;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MaintenanceService
{
    /**
     * Get paginated maintenance records with filters
     */
    public function getPaginatedMaintenanceRecords(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = TruckMaintenanceRecord::with(['truck'])
            ->latest('created_at');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('truck_id')) {
            $query->byTruck((int) $request->truck_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('cost_min') && $request->filled('cost_max')) {
            $query->byCostRange((float) $request->cost_min, (float) $request->cost_max);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new maintenance record
     */
    public function createMaintenance(array $data): TruckMaintenanceRecord
    {
        $maintenance = TruckMaintenanceRecord::create($data);

        // Log audit trail
        AuditTrailService::log('create', 'Maintenance', "Maintenance record for '{$maintenance->truck->registration_number}' was created");

        return $maintenance;
    }

    /**
     * Update an existing maintenance record
     */
    public function updateMaintenance(TruckMaintenanceRecord $maintenance, array $data): TruckMaintenanceRecord
    {
        $maintenance->update($data);

        // Log audit trail
        AuditTrailService::log('update', 'Maintenance', "Maintenance record for '{$maintenance->truck->registration_number}' was updated");

        return $maintenance;
    }

    /**
     * Delete a maintenance record (soft delete)
     */
    public function deleteMaintenance(TruckMaintenanceRecord $maintenance): bool
    {
        $truckNumber = $maintenance->truck->registration_number;

        $result = $maintenance->delete();

        // Log audit trail
        AuditTrailService::log('delete', 'Maintenance', "Maintenance record for '{$truckNumber}' was deleted");

        return $result;
    }

    /**
     * Restore a soft-deleted maintenance record
     */
    public function restoreMaintenance(TruckMaintenanceRecord $maintenance): bool
    {
        $result = $maintenance->restore();

        // Log audit trail
        AuditTrailService::log('restore', 'Maintenance', "Maintenance record for '{$maintenance->truck->registration_number}' was restored");

        return $result;
    }

    /**
     * Permanently delete a maintenance record
     */
    public function forceDeleteMaintenance(TruckMaintenanceRecord $maintenance): bool
    {
        $truckNumber = $maintenance->truck->registration_number;

        $result = $maintenance->forceDelete();

        // Log audit trail
        AuditTrailService::log('force_delete', 'Maintenance', "Maintenance record for '{$truckNumber}' was permanently deleted");

        return $result;
    }

    /**
     * Get maintenance statistics
     */
    public function getMaintenanceStatistics(): array
    {
        $totalMaintenance = TruckMaintenanceRecord::count();
        $totalCost = TruckMaintenanceRecord::sum('cost_of_maintenance');
        $averageCost = $totalMaintenance > 0 ? $totalCost / $totalMaintenance : 0;
        $recentMaintenance = TruckMaintenanceRecord::recent(30)->count();
        $activeMaintenance = TruckMaintenanceRecord::active()->count();
        $inactiveMaintenance = TruckMaintenanceRecord::inactive()->count();

        return [
            'total' => $totalMaintenance,
            'total_cost' => $totalCost,
            'average_cost' => $averageCost,
            'recent' => $recentMaintenance,
            'active' => $activeMaintenance,
            'inactive' => $inactiveMaintenance,
        ];
    }

    /**
     * Export maintenance records data
     */
    public function exportMaintenanceRecords(Request $request): Collection
    {
        $query = TruckMaintenanceRecord::with(['truck']);

        // Apply same filters as pagination
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('truck_id')) {
            $query->byTruck((int) $request->truck_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('cost_min') && $request->filled('cost_max')) {
            $query->byCostRange((float) $request->cost_min, (float) $request->cost_max);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $maintenanceRecords = $query->get();

        // Log audit trail
        AuditTrailService::log('export', 'Maintenance', "Maintenance records data exported ({$maintenanceRecords->count()} records)");

        return $maintenanceRecords;
    }

    /**
     * Get maintenance records for a specific truck
     */
    public function getTruckMaintenanceRecords(int $truckId, int $perPage = 15): LengthAwarePaginator
    {
        return TruckMaintenanceRecord::where('truck_id', $truckId)
            ->with(['truck'])
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Get maintenance cost summary by truck
     */
    public function getMaintenanceCostSummary(): Collection
    {
        return TruckMaintenanceRecord::with(['truck'])
            ->selectRaw('truck_id, SUM(cost_of_maintenance) as total_cost, COUNT(*) as maintenance_count')
            ->groupBy('truck_id')
            ->orderBy('total_cost', 'desc')
            ->get();
    }

    /**
     * Get maintenance alerts (high cost, recent maintenance, etc.)
     */
    public function getMaintenanceAlerts(): array
    {
        $highCostThreshold = 10000; // Alert for maintenance over 10,000
        $recentDays = 7; // Alert for maintenance in last 7 days

        $highCostMaintenance = TruckMaintenanceRecord::where('cost_of_maintenance', '>', $highCostThreshold)
            ->with(['truck'])
            ->recent(30)
            ->get();

        $recentMaintenance = TruckMaintenanceRecord::recent($recentDays)
            ->with(['truck'])
            ->get();

        return [
            'high_cost' => $highCostMaintenance,
            'recent' => $recentMaintenance,
        ];
    }
}
