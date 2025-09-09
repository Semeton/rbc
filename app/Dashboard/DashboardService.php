<?php

declare(strict_types=1);

namespace App\Dashboard;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getOverviewStats(): array
    {
        $today = now();
        $thisMonth = $today->copy()->startOfMonth();
        $lastMonth = $today->copy()->subMonth()->startOfMonth();

        // Customer stats
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', $thisMonth)->count();

        // Driver stats
        $totalDrivers = Driver::count();
        $activeDrivers = Driver::active()->count();

        // Truck stats
        $totalTrucks = Truck::count();
        $activeTrucks = Truck::active()->count();

        // Transaction stats
        $todayTransactions = DailyCustomerTransaction::whereDate('created_at', $today)->count();
        $monthlyTransactions = DailyCustomerTransaction::where('created_at', '>=', $thisMonth)->count();

        // Revenue calculations
        $todayRevenue = DailyCustomerTransaction::whereDate('created_at', $today)
            ->get()
            ->sum(function ($transaction) {
                return $transaction->atc_cost + $transaction->transport_cost;
            });

        $monthlyRevenue = DailyCustomerTransaction::where('created_at', '>=', $thisMonth)
            ->get()
            ->sum(function ($transaction) {
                return $transaction->atc_cost + $transaction->transport_cost;
            });

        $lastMonthRevenue = DailyCustomerTransaction::whereBetween('created_at', [
            $lastMonth,
            $thisMonth->copy()->subSecond(),
        ])->get()->sum(function ($transaction) {
            return $transaction->atc_cost + $transaction->transport_cost;
        });

        // Payment stats
        $todayPayments = CustomerPayment::whereDate('payment_date', $today)->sum('amount');
        $monthlyPayments = CustomerPayment::where('payment_date', '>=', $thisMonth)->sum('amount');

        // Calculate growth percentages
        $revenueGrowth = $lastMonthRevenue > 0
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        return [
            'customers' => [
                'total' => $totalCustomers,
                'active' => $activeCustomers,
                'new_this_month' => $newCustomersThisMonth,
            ],
            'drivers' => [
                'total' => $totalDrivers,
                'active' => $activeDrivers,
            ],
            'trucks' => [
                'total' => $totalTrucks,
                'active' => $activeTrucks,
            ],
            'transactions' => [
                'today' => $todayTransactions,
                'this_month' => $monthlyTransactions,
            ],
            'revenue' => [
                'today' => $todayRevenue,
                'this_month' => $monthlyRevenue,
                'last_month' => $lastMonthRevenue,
                'growth_percentage' => round($revenueGrowth, 2),
            ],
            'payments' => [
                'today' => $todayPayments,
                'this_month' => $monthlyPayments,
            ],
        ];
    }

    public function getRecentActivity(int $limit = 10): Collection
    {
        $activities = collect();

        // Recent transactions
        $recentTransactions = DailyCustomerTransaction::with(['customer', 'driver'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'transaction',
                    'title' => 'New Transaction',
                    'description' => "Transaction for {$transaction->customer->name} by {$transaction->driver->name}",
                    'amount' => $transaction->atc_cost + $transaction->transport_cost,
                    'date' => $transaction->created_at,
                    'icon' => 'clipboard-document-list',
                    'color' => 'blue',
                ];
            });

        // Recent payments
        $recentPayments = CustomerPayment::with('customer')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment',
                    'title' => 'Payment Received',
                    'description' => "Payment from {$payment->customer->name}",
                    'amount' => $payment->amount,
                    'date' => $payment->payment_date,
                    'icon' => 'banknotes',
                    'color' => 'green',
                ];
            });

        // Recent maintenance
        $recentMaintenance = TruckMaintenanceRecord::with('truck')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($maintenance) {
                return [
                    'type' => 'maintenance',
                    'title' => 'Maintenance Record',
                    'description' => "Maintenance for {$maintenance->truck->registration_number}",
                    'amount' => $maintenance->cost_of_maintenance,
                    'date' => $maintenance->created_at,
                    'icon' => 'wrench-screwdriver',
                    'color' => 'orange',
                ];
            });

        // Combine and sort by date
        $activities = $activities
            ->merge($recentTransactions)
            ->merge($recentPayments)
            ->merge($recentMaintenance)
            ->sortByDesc('date')
            ->take($limit);

        return $activities;
    }

    public function getTopPerformers(): array
    {
        $thisMonth = now()->startOfMonth();

        // Top customers by revenue
        $topCustomers = DailyCustomerTransaction::where('created_at', '>=', $thisMonth)
            ->with('customer')
            ->get()
            ->groupBy('customer_id')
            ->map(function ($transactions, $customerId) {
                $customer = $transactions->first()->customer;
                $totalRevenue = $transactions->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });

                return [
                    'id' => $customerId,
                    'name' => $customer->name,
                    'revenue' => $totalRevenue,
                    'transactions' => $transactions->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->take(5);

        // Top drivers by revenue
        $topDrivers = DailyCustomerTransaction::where('created_at', '>=', $thisMonth)
            ->with('driver')
            ->get()
            ->groupBy('driver_id')
            ->map(function ($transactions, $driverId) {
                $driver = $transactions->first()->driver;
                $totalRevenue = $transactions->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });

                return [
                    'id' => $driverId,
                    'name' => $driver->name,
                    'revenue' => $totalRevenue,
                    'transactions' => $transactions->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->take(5);

        return [
            'customers' => $topCustomers->values(),
            'drivers' => $topDrivers->values(),
        ];
    }

    public function getRevenueChart(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $dailyRevenue = DailyCustomerTransaction::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($transaction) {
                return $transaction->created_at->format('Y-m-d');
            })
            ->map(function ($transactions, $date) {
                return [
                    'date' => $date,
                    'revenue' => $transactions->sum(function ($transaction) {
                        return $transaction->atc_cost + $transaction->transport_cost;
                    }),
                    'transactions' => $transactions->count(),
                ];
            })
            ->sortBy('date');

        return $dailyRevenue->values()->toArray();
    }

    public function getPendingItems(): array
    {
        // Pending ATCs (if any)
        $pendingAtcs = Atc::where('status', false)->count();

        // Customers with outstanding balances
        $outstandingCustomers = Customer::with(['transactions', 'payments'])->get()
            ->filter(function ($customer) {
                $totalTransactions = $customer->transactions->sum(function ($transaction) {
                    return $transaction->atc_cost + $transaction->transport_cost;
                });
                $totalPayments = $customer->payments->sum('amount');

                return $totalTransactions > $totalPayments;
            })
            ->count();

        // Trucks needing maintenance (simplified - trucks with recent maintenance)
        $trucksNeedingMaintenance = Truck::whereDoesntHave('maintenanceRecords', function ($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();

        return [
            'pending_atcs' => $pendingAtcs,
            'outstanding_customers' => $outstandingCustomers,
            'trucks_needing_maintenance' => $trucksNeedingMaintenance,
        ];
    }

    public function getQuickStats(): array
    {
        $today = now();
        $thisWeek = $today->copy()->startOfWeek();
        $thisMonth = $today->copy()->startOfMonth();

        return [
            'today' => [
                'transactions' => DailyCustomerTransaction::whereDate('created_at', $today)->count(),
                'revenue' => DailyCustomerTransaction::whereDate('created_at', $today)
                    ->get()
                    ->sum(function ($transaction) {
                        return $transaction->atc_cost + $transaction->transport_cost;
                    }),
                'payments' => CustomerPayment::whereDate('payment_date', $today)->sum('amount'),
            ],
            'this_week' => [
                'transactions' => DailyCustomerTransaction::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => DailyCustomerTransaction::where('created_at', '>=', $thisWeek)
                    ->get()
                    ->sum(function ($transaction) {
                        return $transaction->atc_cost + $transaction->transport_cost;
                    }),
                'payments' => CustomerPayment::where('payment_date', '>=', $thisWeek)->sum('amount'),
            ],
            'this_month' => [
                'transactions' => DailyCustomerTransaction::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => DailyCustomerTransaction::where('created_at', '>=', $thisMonth)
                    ->get()
                    ->sum(function ($transaction) {
                        return $transaction->atc_cost + $transaction->transport_cost;
                    }),
                'payments' => CustomerPayment::where('payment_date', '>=', $thisMonth)->sum('amount'),
            ],
        ];
    }
}
