<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Atc;
use App\Models\DailyCustomerTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AtcAllocationValidator
{
    /**
     * Validate that the total tons allocated to an ATC doesn't exceed the ATC's total tons
     */
    public function validateAtcAllocation(Atc $atc, ?float $excludeTransactionId = null): bool
    {
        $query = $atc->transactions();
        
        if ($excludeTransactionId) {
            $query->where('id', '!=', $excludeTransactionId);
        }
        
        $allocatedTons = $query->sum('tons') ?? 0;
        
        return $allocatedTons <= $atc->tons;
    }

    /**
     * Get the remaining tons available for allocation for an ATC
     */
    public function getRemainingTons(Atc $atc, ?int $excludeTransactionId = null): float
    {
        $query = $atc->transactions();
        
        if ($excludeTransactionId) {
            $query->where('id', '!=', $excludeTransactionId);
        }
        
        $allocatedTons = $query->sum('tons') ?? 0;
        
        return max(0, $atc->tons - $allocatedTons);
    }

    /**
     * Validate a transaction's tons against the ATC's remaining capacity
     */
    public function validateTransactionTons(DailyCustomerTransaction $transaction): bool
    {
        $remainingTons = $this->getRemainingTons($transaction->atc, $transaction->id);
        
        return $transaction->tons <= $remainingTons;
    }

    /**
     * Validate and throw exception if allocation is invalid
     */
    public function validateOrFail(DailyCustomerTransaction $transaction): void
    {
        if (!$this->validateTransactionTons($transaction)) {
            $remainingTons = $this->getRemainingTons($transaction->atc, $transaction->id);
            
            throw ValidationException::withMessages([
                'tons' => [
                    "The tons allocated ({$transaction->tons}) exceeds the remaining capacity ({$remainingTons}) for ATC #{$transaction->atc->atc_number}."
                ]
            ]);
        }
    }

    /**
     * Get allocation summary for an ATC
     */
    public function getAllocationSummary(Atc $atc): array
    {
        $allocatedTons = $atc->transactions()->sum('tons') ?? 0;
        $remainingTons = max(0, $atc->tons - $allocatedTons);
        $allocationPercentage = $atc->tons > 0 ? ($allocatedTons / $atc->tons) * 100 : 0;
        
        return [
            'total_tons' => $atc->tons,
            'allocated_tons' => $allocatedTons,
            'remaining_tons' => $remainingTons,
            'allocation_percentage' => round($allocationPercentage, 2),
            'is_fully_allocated' => $remainingTons <= 0,
            'is_over_allocated' => $allocatedTons > $atc->tons,
            'transactions_count' => $atc->transactions()->count(),
        ];
    }

    /**
     * Get all ATCs with their allocation status
     */
    public function getAllAtcsWithAllocationStatus(): array
    {
        return Atc::with('transactions')
            ->get()
            ->map(function (Atc $atc) {
                return [
                    'atc' => $atc,
                    'allocation' => $this->getAllocationSummary($atc),
                ];
            })
            ->toArray();
    }

    /**
     * Find ATCs that are over-allocated
     */
    public function getOverAllocatedAtcs(): array
    {
        return Atc::with('transactions')
            ->get()
            ->filter(function (Atc $atc) {
                return $this->getAllocationSummary($atc)['is_over_allocated'];
            })
            ->map(function (Atc $atc) {
                return [
                    'atc' => $atc,
                    'allocation' => $this->getAllocationSummary($atc),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Find ATCs that are fully allocated
     */
    public function getFullyAllocatedAtcs(): array
    {
        return Atc::with('transactions')
            ->get()
            ->filter(function (Atc $atc) {
                return $this->getAllocationSummary($atc)['is_fully_allocated'];
            })
            ->map(function (Atc $atc) {
                return [
                    'atc' => $atc,
                    'allocation' => $this->getAllocationSummary($atc),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Find ATCs with available capacity
     */
    public function getAtcsWithAvailableCapacity(): array
    {
        return Atc::with('transactions')
            ->get()
            ->filter(function (Atc $atc) {
                $summary = $this->getAllocationSummary($atc);
                return !$summary['is_fully_allocated'] && !$summary['is_over_allocated'];
            })
            ->map(function (Atc $atc) {
                return [
                    'atc' => $atc,
                    'allocation' => $this->getAllocationSummary($atc),
                ];
            })
            ->values()
            ->toArray();
    }
}