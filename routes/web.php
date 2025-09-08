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
    });
});

require __DIR__.'/auth.php';
