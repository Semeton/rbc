<?php

declare(strict_types=1);

namespace App\Livewire\Payment;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|exists:customers,id')]
    public int $customer_id = 0;

    #[Validate('required|date|before_or_equal:today')]
    public string $payment_date = '';

    #[Validate('required|numeric|min:0.01')]
    public float $amount = 0.0;

    #[Validate('nullable|string|max:255')]
    public ?string $bank_name = null;

    #[Validate('nullable|string|max:1000')]
    public ?string $notes = null;

    public function mount(): void
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function store(): void
    {
        $this->validate();

        $payment = app(\App\Payment\Services\PaymentService::class)->createPayment([
            'customer_id' => $this->customer_id,
            'payment_date' => $this->payment_date,
            'amount' => $this->amount,
            'bank_name' => $this->bank_name,
            'notes' => $this->notes,
        ]);

        $this->redirect(route('payments.show', $payment));
    }

    #[Computed]
    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.payment.create');
    }
}
