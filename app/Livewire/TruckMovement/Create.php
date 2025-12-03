<?php

declare(strict_types=1);

namespace App\Livewire\TruckMovement;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\DailyTruckRecord;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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

    #[Validate('required|integer|exists:atcs,id', message: 'Please select a valid ATC.')]
    public int $atc_id = 0;

    #[Validate('required|date')]
    public string $atc_collection_date = '';

    #[Validate('required|date|after_or_equal:atc_collection_date')]
    public string $load_dispatch_date = '';

    #[Validate('required|numeric|min:0')]
    public ?float $customer_cost = null;

    /**
     * Snapshot of the selected ATC's cost for UI preview.
     */
    public float $atc_cost = 0.0;

    /**
     * Fare preview (Customer Cost - ATC Cost).
     */
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

    /**
     * Total preview (Fare - Gas + Haulage).
     */
    public float $total_amount = 0.0;

    /**
     * Total + Incentive preview.
     */
    public float $total_plus_incentive = 0.0;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function mount(): void
    {
        $this->atc_collection_date = now()->format('Y-m-d');
        $this->load_dispatch_date = now()->format('Y-m-d');
        $this->synchronizeFinancialSnapshots();
    }

    public function hydrate(): void
    {
        $this->synchronizeFinancialSnapshots();
    }

    public function store(): void
    {
        // Ensure atc_id is properly cast before validation
        $this->atc_id = (int) $this->atc_id;
        
        $this->synchronizeFinancialSnapshots();
        $this->validate();

        $truckMovement = app(\App\TruckMovement\Services\TruckMovementService::class)->createTruckMovement([
            'driver_id' => $this->driver_id,
            'truck_id' => $this->truck_id,
            'customer_id' => $this->customer_id,
            'atc_id' => $this->atc_id,
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

        $this->redirect(route('truck-movements.show', $truckMovement));
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

    #[Computed]
    public function atcs(): EloquentCollection
    {
        return Atc::orderBy('atc_number')->get();
    }

    #[Computed]
    public function recentTruckMovements(): EloquentCollection
    {
        return DailyTruckRecord::with(['driver:id,name', 'truck:id,registration_number,cab_number', 'customer:id,name'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.truck-movement.create');
    }

    public function updatedAtcId($value): void
    {
        // Ensure atc_id is cast to integer (Select2 may send string)
        // Handle empty string or null values
        if (empty($value) || $value === '' || $value === null) {
            $this->atc_id = 0;
        } else {
            $this->atc_id = (int) $value;
        }
        
        // Only sync if we have a valid ATC ID
        if ($this->atc_id > 0) {
            $this->synchronizeFinancialSnapshots();
        } else {
            // Reset derived values if no ATC selected
            $this->atc_cost = 0.0;
            $this->fare = max(0.0, (float) $this->customer_cost);
            $this->synchronizeFinancialSnapshots();
        }
    }

    public function updatedCustomerCost(): void
    {
        $this->synchronizeFinancialSnapshots();
    }

    public function updatedGasChopMoney(): void
    {
        $this->synchronizeFinancialSnapshots();
    }

    public function updatedHaulage(): void
    {
        $this->synchronizeFinancialSnapshots();
    }

    public function updatedIncentive(): void
    {
        $this->synchronizeFinancialSnapshots();
    }

    /**
     * Keep derived financial fields in sync with base inputs.
     */
    private function synchronizeFinancialSnapshots(): void
    {
        $this->atc_cost = $this->resolveAtcCost();
        $this->fare = max(0.0, (float) $this->customer_cost - $this->atc_cost);

        $haulage = $this->haulage ?? 0.0;
        $incentive = $this->incentive ?? 0.0;

        $this->total_amount = $this->fare - (float) $this->gas_chop_money + $haulage;
        $this->total_plus_incentive = $this->total_amount + $incentive;
    }

    private function resolveAtcCost(): float
    {
        if ($this->atc_id === 0) {
            return 0.0;
        }

        /** @var \App\Models\Atc|null $atc */
        $atc = Atc::find($this->atc_id);

        return $atc?->amount ? (float) $atc->amount : 0.0;
    }
}
