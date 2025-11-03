<?php

use App\Livewire\Atc\AtcAllocationManager;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::redirect('/', '/dashboard')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Admin-only routes (full control)
    Route::middleware(['role:admin'])->group(function () {
        // User Management Routes
        Route::get('/users', \App\Livewire\User\UserManager::class)->name('users.index');
        Route::get('/users/invite', \App\Livewire\User\InviteUser::class)->name('users.invite');

        // Audit Trails Route
        Route::get('/audit-trails', \App\Livewire\AuditTrail\Index::class)->name('audit-trails.index');
    });

    // Admin and Operations Manager routes (CRUD operations)
    Route::middleware(['role:admin,operations_manager'])->group(function () {});

    // Admin, Operations Manager, and Staff routes (view and create operations)
    Route::middleware(['role:admin,operations_manager,staff'])->group(function () {
        // Customer Management Routes (view and create)
        Route::get('/customers', function () {
            return view('customers.index');
        })->name('customers.index');
        Route::get('/customers/create', function () {
            return view('customers.create');
        })->name('customers.create');
        Route::get('/customers/{customer}', function (\App\Models\Customer $customer) {
            return view('customers.show', compact('customer'));
        })->name('customers.show');

        // Driver Management Routes (view and create)
        Route::get('/drivers', function () {
            return view('drivers.index');
        })->name('drivers.index');
        Route::get('/drivers/create', function () {
            return view('drivers.create');
        })->name('drivers.create');
        Route::get('/drivers/{driver}', function (\App\Models\Driver $driver) {
            return view('drivers.show', compact('driver'));
        })->name('drivers.show');

        // Truck Management Routes (view and create)
        Route::get('/trucks', function () {
            return view('trucks.index');
        })->name('trucks.index');
        Route::get('/trucks/create', function () {
            return view('trucks.create');
        })->name('trucks.create');
        Route::get('/trucks/{truck}', function (\App\Models\Truck $truck) {
            return view('trucks.show', compact('truck'));
        })->name('trucks.show');

        // Transaction Management Routes (view and create)
        Route::get('/transactions', \App\Livewire\Transaction\Index::class)->name('transactions.index');
        Route::get('/transactions/create', \App\Livewire\Transaction\Create::class)->name('transactions.create');
        Route::get('/transactions/{transaction}', function (\App\Models\DailyCustomerTransaction $transaction) {
            AuditTrailService::log('page_view', 'Transactions', "Viewed transaction details page for ID: {$transaction->id}");

            return view('transactions.show', compact('transaction'));
        })->name('transactions.show');
        Route::get('/transactions/{transaction}/edit', function (\App\Models\DailyCustomerTransaction $transaction) {
            AuditTrailService::log('page_view', 'Transactions', "Viewed transaction edit page for ID: {$transaction->id}");

            return view('transactions.edit', compact('transaction'));
        })->name('transactions.edit');
        Route::put('/transactions/{transaction}', function (\App\Models\DailyCustomerTransaction $transaction, \Illuminate\Http\Request $request) {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'driver_id' => 'required|exists:drivers,id',
                'atc_id' => 'required|exists:atcs,id',
                'date' => 'required|date',
                'origin' => 'required|string|max:255',
                'destination' => 'required|string|max:255',
                'cement_type' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'atc_cost' => 'required|numeric|min:0',
                'transport_cost' => 'required|numeric|min:0',
                'tons' => 'required|numeric|min:0',
                'deport_details' => 'nullable|string',
            ]);

            $transaction->update([
                'customer_id' => $request->customer_id,
                'driver_id' => $request->driver_id,
                'atc_id' => $request->atc_id,
                'date' => $request->date,
                'origin' => $request->origin,
                'destination' => $request->destination,
                'cement_type' => $request->cement_type,
                'status' => $request->status === 'active',
                'atc_cost' => $request->atc_cost,
                'transport_cost' => $request->transport_cost,
                'tons' => $request->tons,
                'deport_details' => $request->deport_details ?: null,
            ]);

            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaction updated successfully.');
        })->name('transactions.update');
    });

    // ATC routes (admin/ops/staff/accountant)
    Route::middleware(['role:admin,operations_manager,staff,accountant'])->group(function () {
        Route::get('/atcs', \App\Livewire\Atc\Index::class)->name('atcs.index');
        Route::get('/atcs/create', function () {
            return view('atcs.create');
        })->name('atcs.create');
        Route::get('/atcs/{atc}', function (\App\Models\Atc $atc) {
            return view('atcs.show', compact('atc'));
        })->name('atcs.show');
    });

    // Truck Movement & Maintenance (admin/ops/staff/movement_staff)
    Route::middleware(['role:admin,operations_manager,staff,movement_staff'])->group(function () {
        // Truck Movements
        Route::get('/truck-movements', function () {
            return view('truck-movements.index');
        })->name('truck-movements.index');
        Route::get('/truck-movements/create', function () {
            return view('truck-movements.create');
        })->name('truck-movements.create');
        Route::get('/truck-movements/{truckMovement}', function (\App\Models\DailyTruckRecord $truckMovement) {
            return view('truck-movements.show', compact('truckMovement'));
        })->name('truck-movements.show');

        // Maintenance
        Route::get('/maintenance', function () {
            return view('maintenance.index');
        })->name('maintenance.index');
        Route::get('/maintenance/create', function () {
            return view('maintenance.create');
        })->name('maintenance.create');
        Route::get('/maintenance/{maintenance}', function (\App\Models\TruckMaintenanceRecord $maintenance) {
            return view('maintenance.show', compact('maintenance'));
        })->name('maintenance.show');
    });

    // Reports (admin/ops/staff/accountant/movement_staff)
    Route::middleware(['role:admin,operations_manager,staff,accountant,movement_staff'])->group(function () {
        Route::get('/reports', [\App\Reports\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/customer-balance', function (\App\Models\Customer $customer) {
            return view('reports.customer-balance', compact('customer'));
        })->name('reports.customer-balance');
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
        Route::get('/reports/truck-maintenance-cost', function () {
            return view('reports.truck-maintenance-cost');
        })->name('reports.truck-maintenance-cost');
        Route::get('/reports/pending-atc', function () {
            return view('reports.pending-atc');
        })->name('reports.pending-atc');
        Route::get('/reports/cash-flow', function () {
            return view('reports.cash-flow');
        })->name('reports.cash-flow');
        Route::get('/reports/daily-activity-summary', function () {
            return view('reports.daily-activity-summary');
        })->name('reports.daily-activity-summary');
        Route::get('/reports/profit-estimate', function () {
            return view('reports.profit-estimate');
        })->name('reports.profit-estimate');

        // Notifications Routes (view only)
        Route::get('/notifications', \App\Livewire\Notification\Index::class)->name('notifications.index');
    });

    // Admin and Operations Manager routes (edit operations)
    Route::middleware(['role:admin,operations_manager'])->group(function () {
        Route::get('/customers/{customer}/edit', function (\App\Models\Customer $customer) {
            return view('customers.edit', compact('customer'));
        })->name('customers.edit');
        Route::get('/drivers/{driver}/edit', function (\App\Models\Driver $driver) {
            return view('drivers.edit', compact('driver'));
        })->name('drivers.edit');
        Route::get('/trucks/{truck}/edit', function (\App\Models\Truck $truck) {
            return view('trucks.edit', compact('truck'));
        })->name('trucks.edit');
        Route::get('/atcs/{atc}/edit', function (\App\Models\Atc $atc) {
            return view('atcs.edit', compact('atc'));
        })->name('atcs.edit');
        Route::get('/truck-movements/{truckMovement}/edit', function (\App\Models\DailyTruckRecord $truckMovement) {
            return view('truck-movements.edit', compact('truckMovement'));
        })->name('truck-movements.edit');
        Route::get('/maintenance/{maintenance}/edit', function (\App\Models\TruckMaintenanceRecord $maintenance) {
            return view('maintenance.edit', compact('maintenance'));
        })->name('maintenance.edit');
    });

    // Admin and Accountant routes (payment management)
    Route::middleware(['role:admin,accountant'])->group(function () {
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
    });

    // Admin and Operations Manager routes (ATC allocation)
    Route::middleware(['role:admin,operations_manager'])->group(function () {
        Route::get('/atc/allocation-manager', AtcAllocationManager::class)->name('atc.allocation-manager');
    });

    // Dashboard Routes (all authenticated users)
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard.index');
});

// Invitation acceptance (public route)
Route::get('/invitations/accept/{token}', \App\Livewire\User\AcceptInvitation::class)->name('invitations.accept');

require __DIR__.'/auth.php';
