<?php

declare(strict_types=1);

namespace App\Livewire\Maintenance;

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
    public ?int $truck_id = null;

    #[Url]
    public string $status = '';

    #[Url]
    public ?float $cost_min = null;

    #[Url]
    public ?float $cost_max = null;

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

    public function updatedTruckId(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCostMin(): void
    {
        $this->resetPage();
    }

    public function updatedCostMax(): void
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
        $this->truck_id = null;
        $this->status = '';
        $this->cost_min = null;
        $this->cost_max = null;
        $this->date_from = '';
        $this->date_to = '';
        $this->perPage = 15;
        $this->resetPage();
    }

    #[Computed]
    public function maintenanceRecords()
    {
        $request = app(Request::class);
        $request->merge([
            'search' => $this->search,
            'truck_id' => $this->truck_id,
            'status' => $this->status,
            'cost_min' => $this->cost_min,
            'cost_max' => $this->cost_max,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ]);

        return app(\App\Maintenance\Services\MaintenanceService::class)->getPaginatedMaintenanceRecords($request, $this->perPage);
    }

    #[Computed]
    public function statistics(): array
    {
        return app(\App\Maintenance\Services\MaintenanceService::class)->getMaintenanceStatistics();
    }

    #[Computed]
    public function trucks()
    {
        return Truck::orderBy('registration_number')->get();
    }

    public function render(): View
    {
        return view('livewire.maintenance.index');
    }
}
