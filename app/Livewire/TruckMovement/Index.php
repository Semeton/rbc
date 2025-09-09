<?php

declare(strict_types=1);

namespace App\Livewire\TruckMovement;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Truck;
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
    public ?int $driver_id = null;

    #[Url]
    public ?int $truck_id = null;

    #[Url]
    public ?int $customer_id = null;

    #[Url]
    public string $status = '';

    #[Url]
    public string $date_from = '';

    #[Url]
    public string $date_to = '';

    #[Url]
    public int $perPage = 15;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDriverId(): void
    {
        $this->resetPage();
    }

    public function updatedTruckId(): void
    {
        $this->resetPage();
    }

    public function updatedCustomerId(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->driver_id = null;
        $this->truck_id = null;
        $this->customer_id = null;
        $this->status = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->perPage = 15;
        $this->resetPage();
    }

    #[Computed]
    public function truckMovements()
    {
        $request = app(Request::class);
        $request->merge([
            'search' => $this->search,
            'driver_id' => $this->driver_id,
            'truck_id' => $this->truck_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ]);

        return app(\App\TruckMovement\Services\TruckMovementService::class)->getPaginatedTruckMovements($request, $this->perPage);
    }

    #[Computed]
    public function statistics(): array
    {
        return app(\App\TruckMovement\Services\TruckMovementService::class)->getTruckMovementStatistics();
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
        return view('livewire.truck-movement.index');
    }
}
