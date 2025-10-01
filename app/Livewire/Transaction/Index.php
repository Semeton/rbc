<?php

declare(strict_types=1);

namespace App\Livewire\Transaction;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\Driver;
use App\Services\AuditTrailService;
use App\Transaction\Services\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // UI state
    #[Url]
    public string $search = '';

    #[Url]
    public string $filter = 'all';

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
        $request = new Request([
            'search' => $this->search,
            'status' => $this->filter === 'all' ? null : ($this->filter === 'active'),
            'customer_id' => $this->customer_id ?: null,
            'driver_id' => $this->driver_id ?: null,
            'atc_id' => $this->atc_id ?: null,
            'date_from' => $this->date_from ?: null,
            'date_to' => $this->date_to ?: null,
            'cement_type' => $this->cement_type ?: null,
        ]);

        $transactionService = app(TransactionService::class);

        return $transactionService->getTransactionsWithAllocationInfo($request, $this->perPage);
    }

    #[Computed]
    public function statistics()
    {
        $transactionService = app(\App\Transaction\Services\TransactionService::class);

        return $transactionService->getTransactionStatistics();
    }

    #[Computed]
    public function atcAllocationStats()
    {
        $transactionService = app(TransactionService::class);

        return $transactionService->getAtcAllocationStatistics();
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

    public function updatedFilter(): void
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
        $this->filter = 'all';
        $this->customer_id = 0;
        $this->driver_id = 0;
        $this->atc_id = 0;
        $this->date_from = '';
        $this->date_to = '';
        $this->cement_type = '';
        $this->perPage = 15;
        $this->resetPage();
    }

    public function deleteTransaction(int $transactionId): void
    {
        $transaction = \App\Models\DailyCustomerTransaction::findOrFail($transactionId);

        // Check if user can delete this transaction

        try {
            $transaction->delete();

            // Reset computed properties to refresh the data
            unset($this->transactions);
            unset($this->statistics);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Transaction deleted successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete transaction: '.$e->getMessage(),
            ]);
        }
    }

    public function mount(): void
    {
        AuditTrailService::log('page_view', 'Transactions', 'Viewed transactions index page');
    }

    public function render(): View
    {
        return view('livewire.transaction.index');
    }
}
