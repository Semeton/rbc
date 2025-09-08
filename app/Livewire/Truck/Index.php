<?php

declare(strict_types=1);

namespace App\Livewire\Truck;

use App\Models\Truck;
use App\Truck\Services\TruckService;
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
    public string $status = '';

    #[Url]
    public string $truck_model = '';

    #[Url]
    public string $year_from = '';

    #[Url]
    public string $year_to = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    #[Url]
    public int $perPage = 15;

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    #[Computed]
    public function trucks()
    {
        $request = new Request([
            'search' => $this->search,
            'status' => $this->status,
            'truck_model' => $this->truck_model,
            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ]);

        return app(TruckService::class)->getPaginatedTrucks($request, $this->perPage);
    }

    #[Computed]
    public function statistics()
    {
        return app(TruckService::class)->getTruckStatistics();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedTruckModel(): void
    {
        $this->resetPage();
    }

    public function updatedYearFrom(): void
    {
        $this->resetPage();
    }

    public function updatedYearTo(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->truck_model = '';
        $this->year_from = '';
        $this->year_to = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function deleteTruck(Truck $truck): void
    {
        app(TruckService::class)->deleteTruck($truck);

        $this->dispatch('truck-deleted');
    }

    public function render(): View
    {
        return view('livewire.truck.index');
    }
}
