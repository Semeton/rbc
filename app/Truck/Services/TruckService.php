<?php

declare(strict_types=1);

namespace App\Truck\Services;

use App\Models\Truck;
use App\Services\AuditTrailService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TruckService
{
    /**
     * Get paginated trucks with search and filtering.
     */
    public function getPaginatedTrucks(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Truck::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $statusValue = $request->get('status') === 'active';
            $query->where('status', $statusValue);
        }

        // Apply model filter
        if ($request->filled('truck_model')) {
            $query->where('truck_model', 'like', '%'.$request->get('truck_model').'%');
        }

        // Apply year range filter
        if ($request->filled('year_from') && $request->filled('year_to')) {
            $query->byYearRange(
                (int) $request->get('year_from'),
                (int) $request->get('year_to')
            );
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active trucks.
     */
    public function getActiveTrucks(): Collection
    {
        return Truck::active()->orderBy('registration_number')->get();
    }

    /**
     * Create a new truck.
     */
    public function createTruck(array $data): Truck
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        $truck = Truck::create($data);

        AuditTrailService::log(
            'create',
            'Truck',
            "Truck '{$truck->registration_number}' was created successfully"
        );

        return $truck;
    }

    /**
     * Update an existing truck.
     */
    public function updateTruck(Truck $truck, array $data): Truck
    {
        $originalData = $truck->toArray();

        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        $truck->update($data);

        AuditTrailService::log(
            'update',
            'Truck',
            "Truck '{$truck->registration_number}' was updated successfully"
        );

        return $truck;
    }

    /**
     * Delete a truck (soft delete).
     */
    public function deleteTruck(Truck $truck): bool
    {
        $result = $truck->delete();

        if ($result) {
            AuditTrailService::log(
                'delete',
                'Truck',
                "Truck '{$truck->registration_number}' was deleted successfully"
            );
        }

        return $result;
    }

    /**
     * Restore a soft-deleted truck.
     */
    public function restoreTruck(Truck $truck): bool
    {
        $result = $truck->restore();

        if ($result) {
            AuditTrailService::log(
                'restore',
                'Truck',
                "Truck '{$truck->registration_number}' was restored successfully"
            );
        }

        return $result;
    }

    /**
     * Permanently delete a truck.
     */
    public function forceDeleteTruck(Truck $truck): bool
    {
        $truckData = $truck->toArray();

        $result = $truck->forceDelete();

        if ($result) {
            AuditTrailService::log(
                'force_delete',
                'Truck',
                "Truck '{$truckData['registration_number']}' was permanently deleted"
            );
        }

        return $result;
    }

    /**
     * Get truck statistics.
     */
    public function getTruckStatistics(): array
    {
        return [
            'total' => Truck::count(),
            'active' => Truck::active()->count(),
            'inactive' => Truck::inactive()->count(),
            'recent' => Truck::where('created_at', '>=', now()->subDays(30))->count(),
            'average_age' => Truck::avg('year_of_manufacture') ?
                round(now()->year - Truck::avg('year_of_manufacture'), 1) : 0,
        ];
    }

    /**
     * Export trucks data.
     */
    public function exportTrucks(Request $request): array
    {
        $query = Truck::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        if ($request->filled('status')) {
            $statusValue = $request->get('status') === 'active';
            $query->where('status', $statusValue);
        }

        if ($request->filled('truck_model')) {
            $query->where('truck_model', 'like', '%'.$request->get('truck_model').'%');
        }

        if ($request->filled('year_from') && $request->filled('year_to')) {
            $query->byYearRange(
                (int) $request->get('year_from'),
                (int) $request->get('year_to')
            );
        }

        $trucks = $query->get();

        AuditTrailService::logDataExport(
            'trucks',
            'array',
            $trucks->count()
        );

        return $trucks->toArray();
    }
}
