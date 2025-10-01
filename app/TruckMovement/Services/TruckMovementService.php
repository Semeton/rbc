<?php

declare(strict_types=1);

namespace App\TruckMovement\Services;

use App\Models\DailyTruckRecord;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TruckMovementService
{
    /**
     * Get paginated truck movements with filters
     */
    public function getPaginatedTruckMovements(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = DailyTruckRecord::with(['driver', 'truck', 'customer'])
            ->latest('atc_collection_date');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('driver_id')) {
            $query->byDriver((int) $request->driver_id);
        }

        if ($request->filled('truck_id')) {
            $query->byTruck((int) $request->truck_id);
        }

        if ($request->filled('customer_id')) {
            $query->byCustomer((int) $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new truck movement
     */
    public function createTruckMovement(array $data): DailyTruckRecord
    {
        // Calculate balance: Fare - Gas Chop + Haulage
        $haulage = isset($data['haulage']) ? (float) $data['haulage'] : 0.0;
        $data['balance'] = ((float) $data['fare']) - ((float) $data['gas_chop_money']) + $haulage;

        $truckMovement = DailyTruckRecord::create($data);

        // Log audit trail
        AuditTrailService::log('create', 'TruckMovement', "Truck movement for '{$truckMovement->truck->registration_number}' was created");

        return $truckMovement;
    }

    /**
     * Update an existing truck movement
     */
    public function updateTruckMovement(DailyTruckRecord $truckMovement, array $data): DailyTruckRecord
    {
        // Calculate balance: Fare - Gas Chop + Haulage
        $haulage = isset($data['haulage']) ? (float) $data['haulage'] : 0.0;
        $data['balance'] = ((float) $data['fare']) - ((float) $data['gas_chop_money']) + $haulage;

        $truckMovement->update($data);

        // Log audit trail
        AuditTrailService::log('update', 'TruckMovement', "Truck movement for '{$truckMovement->truck->registration_number}' was updated");

        return $truckMovement;
    }

    /**
     * Delete a truck movement (soft delete)
     */
    public function deleteTruckMovement(DailyTruckRecord $truckMovement): bool
    {
        $truckNumber = $truckMovement->truck->registration_number;

        $result = $truckMovement->delete();

        // Log audit trail
        AuditTrailService::log('delete', 'TruckMovement', "Truck movement for '{$truckNumber}' was deleted");

        return $result;
    }

    /**
     * Restore a soft-deleted truck movement
     */
    public function restoreTruckMovement(DailyTruckRecord $truckMovement): bool
    {
        $result = $truckMovement->restore();

        // Log audit trail
        AuditTrailService::log('restore', 'TruckMovement', "Truck movement for '{$truckMovement->truck->registration_number}' was restored");

        return $result;
    }

    /**
     * Permanently delete a truck movement
     */
    public function forceDeleteTruckMovement(DailyTruckRecord $truckMovement): bool
    {
        $truckNumber = $truckMovement->truck->registration_number;

        $result = $truckMovement->forceDelete();

        // Log audit trail
        AuditTrailService::log('force_delete', 'TruckMovement', "Truck movement for '{$truckNumber}' was permanently deleted");

        return $result;
    }

    /**
     * Get truck movement statistics
     */
    public function getTruckMovementStatistics(): array
    {
        $totalMovements = DailyTruckRecord::count();
        $totalFare = DailyTruckRecord::sum('fare');
        $totalGasChop = DailyTruckRecord::sum('gas_chop_money');
        $totalBalance = DailyTruckRecord::sum('balance');
        $recentMovements = DailyTruckRecord::recent(30)->count();
        $averageFare = $totalMovements > 0 ? $totalFare / $totalMovements : 0;

        return [
            'total' => $totalMovements,
            'total_fare' => $totalFare,
            'total_gas_chop' => $totalGasChop,
            'total_balance' => $totalBalance,
            'recent' => $recentMovements,
            'average_fare' => $averageFare,
        ];
    }

    /**
     * Export truck movements data
     */
    public function exportTruckMovements(Request $request): Collection
    {
        $query = DailyTruckRecord::with(['driver', 'truck', 'customer']);

        // Apply same filters as pagination
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('driver_id')) {
            $query->byDriver((int) $request->driver_id);
        }

        if ($request->filled('truck_id')) {
            $query->byTruck((int) $request->truck_id);
        }

        if ($request->filled('customer_id')) {
            $query->byCustomer((int) $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $truckMovements = $query->get();

        // Log audit trail
        AuditTrailService::log('export', 'TruckMovement', "Truck movements data exported ({$truckMovements->count()} records)");

        return $truckMovements;
    }

    /**
     * Get movements for a specific driver
     */
    public function getDriverMovements(int $driverId, int $perPage = 15): LengthAwarePaginator
    {
        return DailyTruckRecord::where('driver_id', $driverId)
            ->with(['driver', 'truck', 'customer'])
            ->latest('atc_collection_date')
            ->paginate($perPage);
    }

    /**
     * Get movements for a specific truck
     */
    public function getTruckMovements(int $truckId, int $perPage = 15): LengthAwarePaginator
    {
        return DailyTruckRecord::where('truck_id', $truckId)
            ->with(['driver', 'truck', 'customer'])
            ->latest('atc_collection_date')
            ->paginate($perPage);
    }
}
