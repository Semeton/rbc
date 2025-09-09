<?php

declare(strict_types=1);

namespace App\ATC\Services;

use App\Models\Atc;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ATCService
{
    /**
     * Get paginated ATCs with search and filtering.
     */
    public function getPaginatedATCs(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Atc::query();

        // Search by ATC number
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('status', $status);
        }

        // Filter by ATC type
        if ($request->filled('atc_type')) {
            $query->byType($request->atc_type);
        }

        // Filter by company
        if ($request->filled('company')) {
            $query->where('company', 'like', '%'.$request->company.'%');
        }

        // Filter by amount range
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new ATC.
     */
    public function createATC(array $data): Atc
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        $atc = Atc::create($data);

        AuditTrailService::log('create', 'ATC', "ATC '{$atc->atc_number}' was created");

        return $atc;
    }

    /**
     * Update an existing ATC.
     */
    public function updateATC(Atc $atc, array $data): Atc
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        $atc->update($data);

        AuditTrailService::log('update', 'ATC', "ATC '{$atc->atc_number}' was updated");

        return $atc;
    }

    /**
     * Delete an ATC (soft delete).
     */
    public function deleteATC(Atc $atc): bool
    {
        $atcNumber = $atc->atc_number;
        $result = $atc->delete();

        if ($result) {
            AuditTrailService::log('delete', 'ATC', "ATC '{$atcNumber}' was deleted");
        }

        return $result;
    }

    /**
     * Restore a soft-deleted ATC.
     */
    public function restoreATC(Atc $atc): bool
    {
        $atcNumber = $atc->atc_number;
        $result = $atc->restore();

        if ($result) {
            AuditTrailService::log('restore', 'ATC', "ATC '{$atcNumber}' was restored");
        }

        return $result;
    }

    /**
     * Permanently delete an ATC.
     */
    public function forceDeleteATC(Atc $atc): bool
    {
        $atcNumber = $atc->atc_number;
        $result = $atc->forceDelete();

        if ($result) {
            AuditTrailService::log('force_delete', 'ATC', "ATC '{$atcNumber}' was permanently deleted");
        }

        return $result;
    }

    /**
     * Get ATC statistics.
     */
    public function getATCStatistics(): array
    {
        return [
            'total' => Atc::count(),
            'active' => Atc::active()->count(),
            'inactive' => Atc::where('status', false)->count(),
            'recent' => Atc::where('created_at', '>=', now()->subDays(30))->count(),
            'total_amount' => Atc::sum('amount'),
            'total_tons' => Atc::sum('tons'),
            'bg_count' => Atc::byType('bg')->count(),
            'cash_payment_count' => Atc::byType('cash_payment')->count(),
        ];
    }

    /**
     * Export ATCs data.
     */
    public function exportATCs(Request $request): array
    {
        $query = Atc::query();

        // Apply same filters as pagination
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active';
            $query->where('status', $status);
        }

        if ($request->filled('atc_type')) {
            $query->byType($request->atc_type);
        }

        if ($request->filled('company')) {
            $query->where('company', 'like', '%'.$request->company.'%');
        }

        $atcs = $query->get();

        AuditTrailService::logDataExport('atcs', 'array', $atcs->count());

        return $atcs->toArray();
    }
}
