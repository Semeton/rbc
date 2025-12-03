<?php

declare(strict_types=1);

namespace App\Livewire\TruckMovement;

use App\Models\Customer;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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

    public float $atc_cost = 0.0;

    #[Validate('required|numeric|min:0')]
    public ?float $customer_cost = null;

    #[Validate('required|numeric|min:0')]
    public float $fare = 0.0;

    #[Validate('required|numeric|min:0')]
    public ?float $gas_chop_money = null;

    #[Validate('nullable|numeric')]
    public ?float $haulage = null;

    #[Validate('nullable|numeric')]
    public ?float $incentive = 0.0;

    #[Validate('nullable|numeric|min:0')]
    public ?float $salary_contribution = 0.0;

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
        $this->atc_cost = $truckMovement->atc?->amount !== null ? (float) $truckMovement->atc->amount : 0.0;
        $this->customer_cost = (float) $truckMovement->customer_cost;
        $this->fare = (float) $truckMovement->fare;
        $this->gas_chop_money = (float) $truckMovement->gas_chop_money;
        $this->haulage = $truckMovement->haulage !== null ? (float) $truckMovement->haulage : null;
        $this->incentive = $truckMovement->incentive !== null ? (float) $truckMovement->incentive : 0.0;
        $this->salary_contribution = $truckMovement->salary_contribution !== null ? (float) $truckMovement->salary_contribution : 0.0;
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
            'customer_cost' => $this->customer_cost,
            'fare' => $this->fare,
            'gas_chop_money' => $this->gas_chop_money,
            'haulage' => $this->haulage,
            'incentive' => $this->incentive,
            'salary_contribution' => $this->salary_contribution,
            'status' => $this->status === 'active',
        ]);

        $this->redirect(route('truck-movements.show', $this->truckMovement));
    }

    #[Computed]
    public function drivers(): EloquentCollection
    {
        return Driver::orderBy('name')->get();
    }

    #[Computed]
    public function trucks(): EloquentCollection
    {
        return Truck::orderBy('registration_number')->get();
    }

    #[Computed]
    public function customers(): EloquentCollection
    {
        return Customer::orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.truck-movement.edit');
    }

    public function updatedCustomerCost(): void
    {
        $this->recalculateFare();
    }

    public function updatedGasChopMoney(): void
    {
        // Derived totals are recalculated in the service; this hook exists to keep parity with create.
    }

    public function updatedHaulage(): void
    {
        // Derived totals are recalculated in the service; this hook exists to keep parity with create.
    }

    public function updatedIncentive(): void
    {
        // Derived totals are recalculated in the service; this hook exists to keep parity with create.
    }

    private function recalculateFare(): void
    {
        $atc = $this->truckMovement->atc ?? null;

        $this->atc_cost = $atc !== null ? (float) $atc->amount : 0.0;

        $this->fare = max(0.0, (float) $this->customer_cost - $this->atc_cost);
    }
}
