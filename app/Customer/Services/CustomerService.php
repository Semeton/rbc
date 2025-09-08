<?php

declare(strict_types=1);

namespace App\Customer\Services;

use App\Models\Customer;
use App\Services\AuditTrailService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CustomerService
{
    /**
     * Get paginated customers with search and filtering.
     */
    public function getPaginatedCustomers(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Customer::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $statusValue = $request->get('status') === 'active';
            $query->where('status', $statusValue);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active customers.
     */
    public function getActiveCustomers(): Collection
    {
        return Customer::active()->orderBy('name')->get();
    }

    /**
     * Create a new customer.
     */
    public function createCustomer(array $data): Customer
    {
        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        $customer = Customer::create($data);

        AuditTrailService::log(
            'create',
            'Customer',
            "Customer '{$customer->name}' was created successfully"
        );

        return $customer;
    }

    /**
     * Update an existing customer.
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $originalData = $customer->toArray();

        // Convert status string to boolean
        if (isset($data['status'])) {
            $data['status'] = $data['status'] === 'active';
        }

        $customer->update($data);

        AuditTrailService::log(
            'update',
            'Customer',
            "Customer '{$customer->name}' was updated successfully"
        );

        return $customer;
    }

    /**
     * Delete a customer (soft delete).
     */
    public function deleteCustomer(Customer $customer): bool
    {
        $result = $customer->delete();

        if ($result) {
            AuditTrailService::log(
                'delete',
                'Customer',
                "Customer '{$customer->name}' was deleted successfully"
            );
        }

        return $result;
    }

    /**
     * Restore a soft-deleted customer.
     */
    public function restoreCustomer(Customer $customer): bool
    {
        $result = $customer->restore();

        if ($result) {
            AuditTrailService::log(
                'restore',
                'Customer',
                "Customer '{$customer->name}' was restored successfully"
            );
        }

        return $result;
    }

    /**
     * Permanently delete a customer.
     */
    public function forceDeleteCustomer(Customer $customer): bool
    {
        $customerData = $customer->toArray();
        $result = $customer->forceDelete();

        if ($result) {
            AuditTrailService::log(
                'force_delete',
                'Customer',
                "Customer '{$customerData['name']}' was permanently deleted"
            );
        }

        return $result;
    }

    /**
     * Get customer statistics.
     */
    public function getCustomerStatistics(): array
    {
        return [
            'total' => Customer::count(),
            'active' => Customer::active()->count(),
            'inactive' => Customer::inactive()->count(),
            'recent' => Customer::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    /**
     * Get customers with outstanding balances.
     */
    public function getCustomersWithOutstandingBalances(): Collection
    {
        return Customer::with(['payments', 'transactions'])
            ->get()
            ->filter(function (Customer $customer) {
                return $customer->balance > 0;
            })
            ->sortByDesc('balance');
    }

    /**
     * Export customers data.
     */
    public function exportCustomers(Request $request): array
    {
        $query = Customer::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        if ($request->filled('status')) {
            $statusValue = $request->get('status') === 'active';
            $query->where('status', $statusValue);
        }

        $customers = $query->get();

        AuditTrailService::logDataExport(
            'customers',
            'array',
            $customers->count()
        );

        return $customers->toArray();
    }
}
