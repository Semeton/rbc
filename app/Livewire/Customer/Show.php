<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Customer\Services\CustomerService;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
        // Don't load relationships that don't exist yet
        // $this->customer = $customer->load(['payments', 'transactions']);
    }

    public function deleteCustomer(): void
    {
        app(CustomerService::class)->deleteCustomer($this->customer);

        $this->dispatch('customer-deleted');
        $this->redirect(route('customers.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.customer.show');
    }
}
