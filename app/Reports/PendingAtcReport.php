<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\Atc;
use Illuminate\Support\Collection;

class PendingAtcReport
{
    public function generate(array $filters = []): Collection
    {
        $query = Atc::query();

        // Filter by ATC type if specified
        if (! empty($filters['atc_type'])) {
            $query->where('atc_type', $filters['atc_type']);
        }

        // Filter by status if specified
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by company if specified
        if (! empty($filters['company'])) {
            $query->where('company', 'like', '%'.$filters['company'].'%');
        }

        // Get pending ATCs (not assigned to any customer transactions)
        $query->whereDoesntHave('transactions');

        return $query->get()->map(function ($atc) {
            return [
                'id' => $atc->id,
                'atc_number' => $atc->atc_number,
                'atc_type' => $atc->atc_type,
                'atc_type_display' => $atc->getAtcTypeAttribute(),
                'company' => $atc->company,
                'amount' => $atc->amount,
                'tons' => $atc->tons,
                'status' => $atc->status,
                'status_display' => $atc->getStatusDisplayAttribute(),
                'created_at' => $atc->created_at->format('Y-m-d'),
                'is_available' => $atc->isAvailable(),
                'utilization_status' => 'Unused', // Since these are pending/unused ATCs
            ];
        });
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        $totalAtcs = $data->count();
        $totalValue = $data->sum('amount');
        $totalTons = $data->sum('tons');
        $activeAtcs = $data->where('status', true)->count();
        $inactiveAtcs = $data->where('status', false)->count();

        // Group by ATC type
        $atcTypes = $data->groupBy('atc_type_display')->map(function ($atcs) {
            return [
                'count' => $atcs->count(),
                'total_value' => $atcs->sum('amount'),
                'total_tons' => $atcs->sum('tons'),
            ];
        });

        $averageValue = $totalAtcs > 0 ? $totalValue / $totalAtcs : 0;
        $averageTons = $totalAtcs > 0 ? $totalTons / $totalAtcs : 0;

        // Calculate utilization metrics
        $utilizationRate = 0; // All ATCs in this report are unused (0% utilization)
        $unusedValue = $totalValue; // All value is unused
        $unusedTons = $totalTons; // All tons are unused

        return [
            'total_atcs' => $totalAtcs,
            'total_value' => $totalValue,
            'total_tons' => $totalTons,
            'active_atcs' => $activeAtcs,
            'inactive_atcs' => $inactiveAtcs,
            'average_value' => $averageValue,
            'average_tons' => $averageTons,
            'atc_types' => $atcTypes->toArray(),
            'utilization_rate' => $utilizationRate,
            'unused_value' => $unusedValue,
            'unused_tons' => $unusedTons,
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // ATC Type Distribution
        $typeDistribution = $data->groupBy('atc_type_display')->map(function ($atcs, $type) {
            return [
                'type' => $type,
                'count' => $atcs->count(),
                'total_value' => $atcs->sum('amount'),
            ];
        })->values();

        // Status Distribution
        $statusDistribution = $data->groupBy('status_display')->map(function ($atcs, $status) {
            return [
                'status' => $status,
                'count' => $atcs->count(),
            ];
        })->values();

        // Monthly Trend (based on creation date)
        $monthlyTrend = $data->groupBy(function ($atc) {
            return \Carbon\Carbon::parse($atc['created_at'])->format('Y-m');
        })->map(function ($monthAtcs, $month) {
            return [
                'month' => \Carbon\Carbon::parse($month)->format('M Y'),
                'count' => $monthAtcs->count(),
                'total_value' => $monthAtcs->sum('amount'),
            ];
        })->sortBy('month')->values();

        return [
            'type_distribution' => $typeDistribution->toArray(),
            'status_distribution' => $statusDistribution->toArray(),
            'monthly_trend' => $monthlyTrend->toArray(),
        ];
    }

    public function getAtcTypes(): array
    {
        return [
            'bg' => 'BG',
            'cash_payment' => 'Cash Payment',
        ];
    }

    public function getStatusOptions(): array
    {
        return [
            1 => 'Active',
            0 => 'Inactive',
        ];
    }
}
