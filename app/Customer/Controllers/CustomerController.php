<?php

declare(strict_types=1);

namespace App\Customer\Controllers;

use App\Customer\Requests\StoreCustomerRequest;
use App\Customer\Requests\UpdateCustomerRequest;
use App\Customer\Services\CustomerService;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    /**
     * Display a listing of customers.
     */
    public function index(Request $request): View
    {
        $customers = $this->customerService->getPaginatedCustomers($request);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->createCustomer($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
        $customer->load(['payments', 'transactions']);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerService->updateCustomer($customer, $request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->customerService->deleteCustomer($customer);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Restore the specified customer from storage.
     */
    public function restore(int $id): RedirectResponse
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $this->customerService->restoreCustomer($customer);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer restored successfully.');
    }

    /**
     * Permanently delete the specified customer from storage.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $this->customerService->forceDeleteCustomer($customer);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer permanently deleted.');
    }
}
