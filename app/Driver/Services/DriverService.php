<?php

declare(strict_types=1);

namespace App\Driver\Services;

use App\Models\Driver;
use App\Services\AuditTrailService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DriverService
{
    /**
     * Get paginated drivers with search and filtering.
     */
    public function getPaginatedDrivers(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Driver::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $statusValue = $request->get('status') === 'active';
            $query->where('status', $statusValue);
        }

        // Apply company filter
        if ($request->filled('company')) {
            $query->where('company', 'like', '%'.$request->get('company').'%');
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active drivers.
     */
    public function getActiveDrivers(): Collection
    {
        return Driver::active()->orderBy('name')->get();
    }

    /**
     * Create a new driver.
     */
    public function createDriver(array $data): Driver
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        // Handle photo upload
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->storePhoto($data['photo']);
        }

        $driver = Driver::create($data);

        AuditTrailService::log(
            'create',
            'Driver',
            "Driver '{$driver->name}' was created successfully"
        );

        return $driver;
    }

    /**
     * Update an existing driver.
     */
    public function updateDriver(Driver $driver, array $data): Driver
    {
        $originalData = $driver->toArray();

        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        // Handle photo upload
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Delete old photo if exists
            if ($driver->photo) {
                $this->deletePhoto($driver->photo);
            }
            $data['photo'] = $this->storePhoto($data['photo']);
        }

        $driver->update($data);

        AuditTrailService::log(
            'update',
            'Driver',
            "Driver '{$driver->name}' was updated successfully"
        );

        return $driver;
    }

    /**
     * Delete a driver (soft delete).
     */
    public function deleteDriver(Driver $driver): bool
    {
        $result = $driver->delete();

        if ($result) {
            AuditTrailService::log(
                'delete',
                'Driver',
                "Driver '{$driver->name}' was deleted successfully"
            );
        }

        return $result;
    }

    /**
     * Restore a soft-deleted driver.
     */
    public function restoreDriver(Driver $driver): bool
    {
        $result = $driver->restore();

        if ($result) {
            AuditTrailService::log(
                'restore',
                'Driver',
                "Driver '{$driver->name}' was restored successfully"
            );
        }

        return $result;
    }

    /**
     * Permanently delete a driver.
     */
    public function forceDeleteDriver(Driver $driver): bool
    {
        $driverData = $driver->toArray();

        // Delete photo if exists
        if ($driver->photo) {
            $this->deletePhoto($driver->photo);
        }

        $result = $driver->forceDelete();

        if ($result) {
            AuditTrailService::log(
                'force_delete',
                'Driver',
                "Driver '{$driverData['name']}' was permanently deleted"
            );
        }

        return $result;
    }

    /**
     * Get driver statistics.
     */
    public function getDriverStatistics(): array
    {
        return [
            'total' => Driver::count(),
            'active' => Driver::active()->count(),
            'inactive' => Driver::inactive()->count(),
            'recent' => Driver::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    /**
     * Store driver photo.
     */
    private function storePhoto(UploadedFile $photo): string
    {
        $filename = time().'_'.uniqid().'.'.$photo->getClientOriginalExtension();

        return $photo->storeAs('drivers/photos', $filename, 'public');
    }

    /**
     * Delete driver photo.
     */
    private function deletePhoto(string $photoPath): void
    {
        if (Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }
    }

    /**
     * Export drivers data.
     */
    public function exportDrivers(Request $request): array
    {
        $query = Driver::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        if ($request->filled('status')) {
            $statusValue = $request->get('status') === 'active';
            $query->where('status', $statusValue);
        }

        if ($request->filled('company')) {
            $query->where('company', 'like', '%'.$request->get('company').'%');
        }

        $drivers = $query->get();

        AuditTrailService::logDataExport(
            'drivers',
            'array',
            $drivers->count()
        );

        return $drivers->toArray();
    }
}
