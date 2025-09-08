<?php

namespace App\Livewire;

use App\Models\Atc;
use App\Models\AuditTrail;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public function render()
    {
        // Get dashboard statistics
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::active()->count(),
            'total_drivers' => Driver::count(),
            'active_drivers' => Driver::active()->count(),
            'total_trucks' => Truck::count(),
            'active_trucks' => Truck::active()->count(),
            'total_atcs' => Atc::count(),
            'active_atcs' => Atc::active()->count(),
        ];

        // Get recent transactions
        $recentTransactions = DailyCustomerTransaction::with(['customer', 'driver', 'atc'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent truck records
        $recentTruckRecords = DailyTruckRecord::with(['driver', 'truck', 'customer'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent payments
        $recentPayments = CustomerPayment::with('customer')
            ->latest()
            ->limit(5)
            ->get();

        // Get recent audit trails
        $recentAudits = AuditTrail::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Calculate monthly revenue
        $monthlyRevenue = DailyCustomerTransaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('atc_cost') + DailyCustomerTransaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('transport_cost');

        // Calculate monthly payments
        $monthlyPayments = CustomerPayment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        // Calculate outstanding balance
        $outstandingBalance = $monthlyRevenue - $monthlyPayments;

        // Get maintenance alerts
        $maintenanceAlerts = TruckMaintenanceRecord::where('status', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'recentTruckRecords' => $recentTruckRecords,
            'recentPayments' => $recentPayments,
            'recentAudits' => $recentAudits,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyPayments' => $monthlyPayments,
            'outstandingBalance' => $outstandingBalance,
            'maintenanceAlerts' => $maintenanceAlerts,
        ]);
    }
}
