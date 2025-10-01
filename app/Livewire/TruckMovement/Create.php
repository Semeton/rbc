<?php

declare(strict_types=1);

namespace App\Livewire\TruckMovement;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|exists:drivers,id')]
    public int $driver_id = 0;

    #[Validate('required|exists:trucks,id')]
    public int $truck_id = 0;

    #[Validate('required|exists:customers,id')]
    public int $customer_id = 0;

    #[Validate('required|exists:atcs,id')]
    public int $atc_id = 0;

    #[Validate('required|date')]
    public string $atc_collection_date = '';

    #[Validate('required|date|after_or_equal:atc_collection_date')]
    public string $load_dispatch_date = '';

    #[Validate('required|numeric')]
    public float $fare = 0.0;

    #[Validate('required|numeric')]
    public float $gas_chop_money = 0.0;

    #[Validate('nullable|numeric')]
    public ?float $haulage = null;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function mount(): void
    {
        $this->atc_collection_date = now()->format('Y-m-d');
        $this->load_dispatch_date = now()->format('Y-m-d');
    }

    public function store(): void
    {
        $this->validate();

        $truckMovement = app(\App\TruckMovement\Services\TruckMovementService::class)->createTruckMovement([
            'driver_id' => $this->driver_id,
            'truck_id' => $this->truck_id,
            'customer_id' => $this->customer_id,
            'atc_id' => $this->atc_id,
            'atc_collection_date' => $this->atc_collection_date,
            'load_dispatch_date' => $this->load_dispatch_date,
            'fare' => $this->fare,
            'gas_chop_money' => $this->gas_chop_money,
            'haulage' => $this->haulage,
            'status' => $this->status === 'active',
        ]);

        $this->redirect(route('truck-movements.show', $truckMovement));
    }

    #[Computed]
    public function drivers()
    {
        return Driver::orderBy('name')->get();
    }

    #[Computed]
    public function trucks()
    {
        return Truck::orderBy('registration_number')->get();
    }

    #[Computed]
    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    #[Computed]
    public function atcs()
    {
        return Atc::orderBy('atc_number')->get();
    }

    public function render(): View
    {
        return view('livewire.truck-movement.create');
    }
}
