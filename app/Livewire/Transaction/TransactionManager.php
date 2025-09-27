<?php

declare(strict_types=1);

namespace App\Livewire\Transaction;

use App\Models\DailyCustomerTransaction;
use App\Transaction\Services\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    use WithPagination;

    // UI state
    public string $search = '';
    public string $filter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function getTransactionsProperty()
    {
        $request = new Request([
            'search' => $this->search,
            'status' => $this->filter === 'all' ? null : ($this->filter === 'active'),
        ]);

        $transactionService = app(TransactionService::class);
        return $transactionService->getTransactionsWithAllocationInfo($request, 15);
    }

    public function getAtcAllocationStatsProperty(): array
    {
        $transactionService = app(TransactionService::class);
        return $transactionService->getAtcAllocationStatistics();
    }

    public function render(): View
    {
        return view('livewire.transaction.transaction-manager');
    }
}