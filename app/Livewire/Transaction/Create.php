<?php

declare(strict_types=1);

namespace App\Livewire\Transaction;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\Driver;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|exists:customers,id')]
    public int $customer_id = 0;

    #[Validate('required|exists:drivers,id')]
    public int $driver_id = 0;

    #[Validate('required|exists:atcs,id')]
    public int $atc_id = 0;

    #[Validate('required|date')]
    public string $date = '';

    #[Validate('required|string|max:255')]
    public string $origin = '';

    #[Validate('nullable|string|max:500')]
    public string $deport_details = '';

    #[Validate('required|string|max:100')]
    public string $cement_type = '';

    #[Validate('required|string|max:255')]
    public string $destination = '';

    #[Validate('required|numeric|min:0')]
    public float $atc_cost = 0.0;

    #[Validate('required|numeric|min:0')]
    public float $transport_cost = 0.0;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    #[Computed]
    public function customers()
    {
        return Customer::active()->orderBy('name')->get();
    }

    #[Computed]
    public function drivers()
    {
        return Driver::active()->orderBy('name')->get();
    }

    #[Computed]
    public function atcs()
    {
        return Atc::active()->orderBy('atc_number')->get();
    }

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function store(): void
    {
        $this->validate();

        $transactionService = app(\App\Transaction\Services\TransactionService::class);
        $transaction = $transactionService->createTransaction([
            'customer_id' => $this->customer_id,
            'driver_id' => $this->driver_id,
            'atc_id' => $this->atc_id,
            'date' => $this->date,
            'origin' => $this->origin,
            'deport_details' => $this->deport_details,
            'cement_type' => $this->cement_type,
            'destination' => $this->destination,
            'atc_cost' => $this->atc_cost,
            'transport_cost' => $this->transport_cost,
            'status' => $this->status,
        ]);

        $this->redirect(route('transactions.show', $transaction));
    }

    public function render(): View
    {
        return view('livewire.transaction.create');
    }
}
