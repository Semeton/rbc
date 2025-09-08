<?php

declare(strict_types=1);

namespace App\Livewire\Customer;

use App\Customer\Services\CustomerService;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Customer $customer;

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

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
        $this->name = $customer->name ?? '';
        $this->email = $customer->email ?? '';
        $this->phone = $customer->phone ?? '';
        $this->status = $customer->status_string ?? 'active';
        $this->notes = $customer->notes ?? null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($this->customer->id),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'name.max' => 'Customer name cannot exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        app(CustomerService::class)->updateCustomer($this->customer, [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->dispatch('customer-updated', $this->customer->id);

        $this->redirect(route('customers.show', $this->customer), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.customer.edit');
    }
}
