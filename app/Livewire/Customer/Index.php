<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Customer\Services\CustomerService;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    #[Url]
    public int $perPage = 15;

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    #[Computed]
    public function customers()
    {
        $request = new Request([
            'search' => $this->search,
            'status' => $this->status,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ]);

        return app(CustomerService::class)->getPaginatedCustomers($request, $this->perPage);
    }

    #[Computed]
    public function statistics()
    {
        return app(CustomerService::class)->getCustomerStatistics();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function deleteCustomer(Customer $customer): void
    {
        app(CustomerService::class)->deleteCustomer($customer);

        $this->dispatch('customer-deleted');
    }

    public function render(): View
    {
        return view('livewire.customer.index');
    }
}
