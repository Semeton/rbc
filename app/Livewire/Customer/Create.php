<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Customer\Services\CustomerService;
use App\Models\Customer as CustomerModel;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $status = 'active';

    public ?string $notes = null;

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['nullable', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'name.max' => 'Customer name cannot exceed 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'status.in' => 'Status must be either active or inactive.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $customer = app(CustomerService::class)->createCustomer([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status ?: 'active',
            'notes' => $this->notes,
        ]);

        $this->dispatch('customer-created', $customer->id);

        $this->redirect(route('customers.show', $customer), navigate: true);
    }

    #[Computed]
    public function recentCustomers()
    {
        return CustomerModel::latest()->limit(5)->get();
    }

    public function render(): View
    {
        return view('livewire.customer.create');
    }
}
