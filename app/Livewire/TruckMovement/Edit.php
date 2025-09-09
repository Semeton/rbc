<?php

declare(strict_types=1);

namespace App\Livewire\TruckMovement;

use App\Models\Customer;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public DailyTruckRecord $truckMovement;

    #[Validate('required|exists:drivers,id')]
    public int $driver_id = 0;

    #[Validate('required|exists:trucks,id')]
    public int $truck_id = 0;

    #[Validate('required|exists:customers,id')]
    public int $customer_id = 0;

    #[Validate('required|date')]
    public string $atc_collection_date = '';

    #[Validate('required|date|after_or_equal:atc_collection_date')]
    public string $load_dispatch_date = '';

    #[Validate('required|numeric|min:0')]
    public float $fare = 0.0;

    #[Validate('required|numeric|min:0')]
    public float $gas_chop_money = 0.0;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function mount(DailyTruckRecord $truckMovement): void
    {
        $this->truckMovement = $truckMovement;
        $this->driver_id = $truckMovement->driver_id;
        $this->truck_id = $truckMovement->truck_id;
        $this->customer_id = $truckMovement->customer_id;
        $this->atc_collection_date = $truckMovement->atc_collection_date->format('Y-m-d');
        $this->load_dispatch_date = $truckMovement->load_dispatch_date->format('Y-m-d');
        $this->fare = (float) $truckMovement->fare;
        $this->gas_chop_money = (float) $truckMovement->gas_chop_money;
        $this->status = $truckMovement->status_string;
    }

    public function update(): void
    {
        $this->validate();

        app(\App\TruckMovement\Services\TruckMovementService::class)->updateTruckMovement($this->truckMovement, [
            'driver_id' => $this->driver_id,
            'truck_id' => $this->truck_id,
            'customer_id' => $this->customer_id,
            'atc_collection_date' => $this->atc_collection_date,
            'load_dispatch_date' => $this->load_dispatch_date,
            'fare' => $this->fare,
            'gas_chop_money' => $this->gas_chop_money,
            'status' => $this->status === 'active',
        ]);

        $this->redirect(route('truck-movements.show', $this->truckMovement));
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

    public function render(): View
    {
        return view('livewire.truck-movement.edit');
    }
}
