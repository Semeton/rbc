<?php

declare(strict_types=1);

namespace App\Livewire\Driver;

use App\Driver\Services\DriverService;
use App\Models\Driver;
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
    public string $company = '';

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
    public function drivers()
    {
        $request = new Request([
            'search' => $this->search,
            'status' => $this->status,
            'company' => $this->company,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ]);

        return app(DriverService::class)->getPaginatedDrivers($request, $this->perPage);
    }

    #[Computed]
    public function statistics()
    {
        return app(DriverService::class)->getDriverStatistics();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCompany(): void
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
        $this->company = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function deleteDriver(Driver $driver): void
    {
        app(DriverService::class)->deleteDriver($driver);

        $this->dispatch('driver-deleted');
    }

    public function render(): View
    {
        return view('livewire.driver.index');
    }
}
