<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Customer Management Routes
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/customers', function () {
            return view('customers.index');
        })->name('customers.index');

        Route::get('/customers/create', function () {
            return view('customers.create');
        })->name('customers.create');

        Route::get('/customers/{customer}', function (\App\Models\Customer $customer) {
            return view('customers.show', compact('customer'));
        })->name('customers.show');

        Route::get('/customers/{customer}/edit', function (\App\Models\Customer $customer) {
            return view('customers.edit', compact('customer'));
        })->name('customers.edit');

        // Driver Management Routes
        Route::get('/drivers', function () {
            return view('drivers.index');
        })->name('drivers.index');

        Route::get('/drivers/create', function () {
            return view('drivers.create');
        })->name('drivers.create');

        Route::get('/drivers/{driver}', function (\App\Models\Driver $driver) {
            return view('drivers.show', compact('driver'));
        })->name('drivers.show');

        Route::get('/drivers/{driver}/edit', function (\App\Models\Driver $driver) {
            return view('drivers.edit', compact('driver'));
        })->name('drivers.edit');

        // Truck Management Routes
        Route::get('/trucks', function () {
            return view('trucks.index');
        })->name('trucks.index');

        Route::get('/trucks/create', function () {
            return view('trucks.create');
        })->name('trucks.create');

        Route::get('/trucks/{truck}', function (\App\Models\Truck $truck) {
            return view('trucks.show', compact('truck'));
        })->name('trucks.show');

        Route::get('/trucks/{truck}/edit', function (\App\Models\Truck $truck) {
            return view('trucks.edit', compact('truck'));
        })->name('trucks.edit');

        // ATC Management Routes
        Route::get('/atcs', function () {
            return view('atcs.index');
        })->name('atcs.index');

        Route::get('/atcs/create', function () {
            return view('atcs.create');
        })->name('atcs.create');

        Route::get('/atcs/{atc}', function (\App\Models\Atc $atc) {
            return view('atcs.show', compact('atc'));
        })->name('atcs.show');

        Route::get('/atcs/{atc}/edit', function (\App\Models\Atc $atc) {
            return view('atcs.edit', compact('atc'));
        })->name('atcs.edit');

        // Transaction Management Routes
        Route::get('/transactions', function () {
            return view('transactions.index');
        })->name('transactions.index');

        Route::get('/transactions/create', function () {
            return view('transactions.create');
        })->name('transactions.create');

        Route::get('/transactions/{transaction}', function (\App\Models\DailyCustomerTransaction $transaction) {
            return view('transactions.show', compact('transaction'));
        })->name('transactions.show');

        Route::get('/transactions/{transaction}/edit', function (\App\Models\DailyCustomerTransaction $transaction) {
            return view('transactions.edit', compact('transaction'));
        })->name('transactions.edit');

        // Payment Management Routes
        Route::get('/payments', function () {
            return view('payments.index');
        })->name('payments.index');

        Route::get('/payments/create', function () {
            return view('payments.create');
        })->name('payments.create');

        Route::get('/payments/{payment}', function (\App\Models\CustomerPayment $payment) {
            return view('payments.show', compact('payment'));
        })->name('payments.show');

        Route::get('/payments/{payment}/edit', function (\App\Models\CustomerPayment $payment) {
            return view('payments.edit', compact('payment'));
        })->name('payments.edit');

        // Truck Movement Management Routes
        Route::get('/truck-movements', function () {
            return view('truck-movements.index');
        })->name('truck-movements.index');

        Route::get('/truck-movements/create', function () {
            return view('truck-movements.create');
        })->name('truck-movements.create');

        Route::get('/truck-movements/{truckMovement}', function (\App\Models\DailyTruckRecord $truckMovement) {
            return view('truck-movements.show', compact('truckMovement'));
        })->name('truck-movements.show');

        Route::get('/truck-movements/{truckMovement}/edit', function (\App\Models\DailyTruckRecord $truckMovement) {
            return view('truck-movements.edit', compact('truckMovement'));
        })->name('truck-movements.edit');

        // Maintenance Management Routes
        Route::get('/maintenance', function () {
            return view('maintenance.index');
        })->name('maintenance.index');

        Route::get('/maintenance/create', function () {
            return view('maintenance.create');
        })->name('maintenance.create');

        Route::get('/maintenance/{maintenance}', function (\App\Models\TruckMaintenanceRecord $maintenance) {
            return view('maintenance.show', compact('maintenance'));
        })->name('maintenance.show');

        Route::get('/maintenance/{maintenance}/edit', function (\App\Models\TruckMaintenanceRecord $maintenance) {
            return view('maintenance.edit', compact('maintenance'));
        })->name('maintenance.edit');

        // Reports Routes
        Route::get('/reports', [\App\Reports\ReportController::class, 'index'])->name('reports.index');
        // Route::get('/reports/customer-balance', [\App\Reports\ReportController::class, 'customerBalance'])->name('reports.customer-balance');
        Route::get('/reports/customer-balance', function (\App\Models\Customer $customer) {
            return view('reports.customer-balance', compact('customer'));
        })->name('reports.customer-balance');
        Route::get('/reports/monthly-sales', [\App\Reports\ReportController::class, 'monthlySales'])->name('reports.monthly-sales');
        Route::get('/reports/driver-performance', [\App\Reports\ReportController::class, 'driverPerformance'])->name('reports.driver-performance');
        Route::get('/reports/truck-utilization', [\App\Reports\ReportController::class, 'truckUtilization'])->name('reports.truck-utilization');
        Route::get('/reports/maintenance-cost', [\App\Reports\ReportController::class, 'maintenanceCost'])->name('reports.maintenance-cost');
        Route::get('/reports/export/{reportType}', [\App\Reports\ReportController::class, 'export'])->name('reports.export');

        // New Reports Routes
        Route::get('/reports/monthly-sales', function () {
            return view('reports.monthly-sales');
        })->name('reports.monthly-sales');

        Route::get('/reports/customer-payment-history', function () {
            return view('reports.customer-payment-history');
        })->name('reports.customer-payment-history');

        Route::get('/reports/depot-performance', function () {
            return view('reports.depot-performance');
        })->name('reports.depot-performance');

        Route::get('/reports/driver-performance', function () {
            return view('reports.driver-performance');
        })->name('reports.driver-performance');

        Route::get('/reports/truck-utilization', function () {
            return view('reports.truck-utilization');
        })->name('reports.truck-utilization');

        Route::get('/reports/outstanding-balances', function () {
            return view('reports.outstanding-balances');
        })->name('reports.outstanding-balances');

        // Dashboard Routes
        Route::get('/dashboard', function () {
            return view('dashboard.index');
        })->name('dashboard.index');
    });
});

require __DIR__.'/auth.php';
