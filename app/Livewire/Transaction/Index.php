<?php

declare(strict_types=1);

namespace App\Livewire\Transaction;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\Driver;
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
    public int $customer_id = 0;

    #[Url]
    public int $driver_id = 0;

    #[Url]
    public int $atc_id = 0;

    #[Url]
    public string $date_from = '';

    #[Url]
    public string $date_to = '';

    #[Url]
    public string $cement_type = '';

    #[Url]
    public int $perPage = 15;

    #[Computed]
    public function transactions()
    {
        $transactionService = app(\App\Transaction\Services\TransactionService::class);
        $request = app(\Illuminate\Http\Request::class);

        return $transactionService->getPaginatedTransactions($request, $this->perPage);
    }

    #[Computed]
    public function statistics()
    {
        $transactionService = app(\App\Transaction\Services\TransactionService::class);

        return $transactionService->getTransactionStatistics();
    }

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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCustomerId(): void
    {
        $this->resetPage();
    }

    public function updatedDriverId(): void
    {
        $this->resetPage();
    }

    public function updatedAtcId(): void
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

    public function updatedCementType(): void
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
        $this->customer_id = 0;
        $this->driver_id = 0;
        $this->atc_id = 0;
        $this->date_from = '';
        $this->date_to = '';
        $this->cement_type = '';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.transaction.index');
    }
}
