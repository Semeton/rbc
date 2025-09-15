<?php

declare(strict_types=1);

namespace App\Reports;

use App\Models\CustomerPayment;
use App\Models\DailyTruckRecord;
use App\Models\TruckMaintenanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CashFlowReport
{
    public function generate(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        // Get incoming cash (customer payments)
        $incomingCash = $this->getIncomingCash($startDate, $endDate);

        // Get outgoing cash (maintenance, gas & chop, fare)
        $outgoingCash = $this->getOutgoingCash($startDate, $endDate);

        // Combine and format the data
        $cashFlowData = collect();

        // Add incoming cash entries
        foreach ($incomingCash as $payment) {
            $cashFlowData->push([
                'id' => 'payment_'.$payment->id,
                'date' => $payment->payment_date->format('Y-m-d'),
                'type' => 'incoming',
                'category' => 'Customer Payment',
                'description' => 'Payment from '.$payment->customer->name,
                'amount' => $payment->amount,
                'bank_name' => $payment->bank_name,
                'customer_name' => $payment->customer->name,
            ]);
        }

        // Add outgoing cash entries
        foreach ($outgoingCash['maintenance'] as $maintenance) {
            $cashFlowData->push([
                'id' => 'maintenance_'.$maintenance->id,
                'date' => $maintenance->created_at->format('Y-m-d'),
                'type' => 'outgoing',
                'category' => 'Truck Maintenance',
                'description' => $maintenance->description,
                'amount' => $maintenance->cost_of_maintenance,
                'truck_cab_number' => $maintenance->truck->cab_number,
                'status' => $maintenance->status ? 'Completed' : 'Pending',
            ]);
        }

        foreach ($outgoingCash['gas_chop'] as $record) {
            $cashFlowData->push([
                'id' => 'gas_chop_'.$record->id,
                'date' => $record->atc_collection_date->format('Y-m-d'),
                'type' => 'outgoing',
                'category' => 'Gas & Chop Money',
                'description' => 'Gas & Chop for '.$record->truck->cab_number,
                'amount' => $record->gas_chop_money,
                'truck_cab_number' => $record->truck->cab_number,
                'driver_name' => $record->driver->name,
            ]);
        }

        foreach ($outgoingCash['fare'] as $record) {
            $cashFlowData->push([
                'id' => 'fare_'.$record->id,
                'date' => $record->atc_collection_date->format('Y-m-d'),
                'type' => 'outgoing',
                'category' => 'Fare',
                'description' => 'Fare for '.$record->truck->cab_number,
                'amount' => $record->fare,
                'truck_cab_number' => $record->truck->cab_number,
                'driver_name' => $record->driver->name,
            ]);
        }

        // Sort by date
        return $cashFlowData->sortBy('date')->values();
    }

    private function getIncomingCash(string $startDate, string $endDate): Collection
    {
        return CustomerPayment::with('customer')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();
    }

    private function getOutgoingCash(string $startDate, string $endDate): array
    {
        // Get maintenance costs
        $maintenance = TruckMaintenanceRecord::with('truck')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Get gas & chop money
        $gasChop = DailyTruckRecord::with(['truck', 'driver'])
            ->whereBetween('atc_collection_date', [$startDate, $endDate])
            ->where('gas_chop_money', '>', 0)
            ->get();

        // Get fare payments
        $fare = DailyTruckRecord::with(['truck', 'driver'])
            ->whereBetween('atc_collection_date', [$startDate, $endDate])
            ->where('fare', '>', 0)
            ->get();

        return [
            'maintenance' => $maintenance,
            'gas_chop' => $gasChop,
            'fare' => $fare,
        ];
    }

    public function getSummary(array $filters = []): array
    {
        $data = $this->generate($filters);

        $totalIncoming = $data->where('type', 'incoming')->sum('amount');
        $totalOutgoing = $data->where('type', 'outgoing')->sum('amount');
        $netCashFlow = $totalIncoming - $totalOutgoing;

        // Break down outgoing by category
        $outgoingBreakdown = $data->where('type', 'outgoing')
            ->groupBy('category')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        $totalMaintenance = $outgoingBreakdown->get('Truck Maintenance', 0);
        $totalGasChop = $outgoingBreakdown->get('Gas & Chop Money', 0);
        $totalFare = $outgoingBreakdown->get('Fare', 0);

        // Calculate percentages
        $incomingPercentage = $totalIncoming > 0 ? ($totalIncoming / ($totalIncoming + $totalOutgoing)) * 100 : 0;
        $outgoingPercentage = $totalOutgoing > 0 ? ($totalOutgoing / ($totalIncoming + $totalOutgoing)) * 100 : 0;

        // Get transaction counts
        $incomingCount = $data->where('type', 'incoming')->count();
        $outgoingCount = $data->where('type', 'outgoing')->count();

        return [
            'total_incoming' => $totalIncoming,
            'total_outgoing' => $totalOutgoing,
            'net_cash_flow' => $netCashFlow,
            'total_maintenance' => $totalMaintenance,
            'total_gas_chop' => $totalGasChop,
            'total_fare' => $totalFare,
            'incoming_percentage' => $incomingPercentage,
            'outgoing_percentage' => $outgoingPercentage,
            'incoming_count' => $incomingCount,
            'outgoing_count' => $outgoingCount,
            'outgoing_breakdown' => $outgoingBreakdown->toArray(),
        ];
    }

    public function getChartData(array $filters = []): array
    {
        $data = $this->generate($filters);

        // Daily cash flow
        $dailyFlow = $data->groupBy('date')->map(function ($dayData, $date) {
            $incoming = $dayData->where('type', 'incoming')->sum('amount');
            $outgoing = $dayData->where('type', 'outgoing')->sum('amount');

            return [
                'date' => Carbon::parse($date)->format('M d'),
                'incoming' => $incoming,
                'outgoing' => $outgoing,
                'net' => $incoming - $outgoing,
            ];
        })->sortBy('date')->values();

        // Monthly trend (if date range spans multiple months)
        $monthlyTrend = $data->groupBy(function ($item) {
            return Carbon::parse($item['date'])->format('Y-m');
        })->map(function ($monthData, $month) {
            $incoming = $monthData->where('type', 'incoming')->sum('amount');
            $outgoing = $monthData->where('type', 'outgoing')->sum('amount');

            return [
                'month' => Carbon::parse($month)->format('M Y'),
                'incoming' => $incoming,
                'outgoing' => $outgoing,
                'net' => $incoming - $outgoing,
            ];
        })->sortBy('month')->values();

        // Category breakdown
        $categoryBreakdown = $data->groupBy('category')->map(function ($items, $category) {
            $incoming = $items->where('type', 'incoming')->sum('amount');
            $outgoing = $items->where('type', 'outgoing')->sum('amount');

            return [
                'category' => $category,
                'incoming' => $incoming,
                'outgoing' => $outgoing,
                'net' => $incoming - $outgoing,
            ];
        })->values();

        return [
            'daily_flow' => $dailyFlow->toArray(),
            'monthly_trend' => $monthlyTrend->toArray(),
            'category_breakdown' => $categoryBreakdown->toArray(),
        ];
    }
}
