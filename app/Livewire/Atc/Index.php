<?php

declare(strict_types=1);

namespace App\Livewire\Atc;

use Illuminate\Contracts\View\View;
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
    public string $atc_type = '';

    #[Url]
    public string $company = '';

    #[Url]
    public int $perPage = 15;

    #[Computed]
    public function atcs()
    {
        $atcService = app(\App\ATC\Services\ATCService::class);
        $request = app(\Illuminate\Http\Request::class);

        return $atcService->getPaginatedATCs($request, $this->perPage);
    }

    #[Computed]
    public function statistics()
    {
        $atcService = app(\App\ATC\Services\ATCService::class);

        return $atcService->getATCStatistics();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedAtcType(): void
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

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->atc_type = '';
        $this->company = '';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.atc.index');
    }
}
