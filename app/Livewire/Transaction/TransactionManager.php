<?php

declare(strict_types=1);

namespace App\Livewire\Transaction;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\DailyCustomerTransaction;
use App\Models\Driver;
use App\Services\AtcAllocationValidator;
use App\Transaction\Services\TransactionService;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    use WithPagination;

    // Form fields
    public ?int $transactionId = null;
    public int $customer_id = 0;
    public int $driver_id = 0;
    public int $atc_id = 0;
    public string $date = '';
    public string $origin = '';
    public string $deport_details = '';
    public string $cement_type = '';
    public string $destination = '';
    public string $atc_cost = '0';
    public string $transport_cost = '0';
    public string $tons = '0';
    public string $status = 'active';

    // UI state
    public bool $showForm = false;
    public bool $isEditing = false;
    public string $search = '';
    public string $filter = 'all';
    public bool $showAtcAllocation = false;
    public ?Atc $selectedAtc = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'driver_id' => 'required|exists:drivers,id',
        'atc_id' => 'required|exists:atcs,id',
        'date' => 'required|date',
        'origin' => 'required|string|max:255',
        'deport_details' => 'nullable|string|max:500',
        'cement_type' => 'required|string|max:100',
        'destination' => 'required|string|max:255',
        'atc_cost' => 'required|numeric|min:0',
        'transport_cost' => 'required|numeric|min:0',
        'tons' => 'required|numeric|min:0.01',
        'status' => 'required|in:active,inactive',
    ];

    protected function rules()
    {
        $rules = $this->rules;
        
        // Add custom validation for ATC cost calculation
        if ($this->atc_id && $this->tons) {
            $atc = Atc::find($this->atc_id);
            if ($atc) {
                $expectedAtcCost = (float) $this->tons * $atc->price_per_ton;
                $rules['atc_cost'] = [
                    'required',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) use ($expectedAtcCost, $atc) {
                        if (abs((float) $value - $expectedAtcCost) > 0.01) {
                            $fail("ATC cost must be ₦" . number_format($expectedAtcCost, 2) . " (calculated from {$this->tons} tons × ₦" . number_format($atc->price_per_ton, 2) . " per ton).");
                        }
                    }
                ];
            }
        }
        
        return $rules;
    }

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedAtcId(): void
    {
        if ($this->atc_id) {
            $atc = Atc::find($this->atc_id);
            if ($atc) {
                $allocationValidator = app(AtcAllocationValidator::class);
                $remainingTons = $allocationValidator->getRemainingTons($atc, $this->transactionId);
                
                // Auto-fill remaining tons
                $this->tons = (string) $remainingTons;
                
                // Auto-calculate ATC cost based on remaining tons and price per ton
                $calculatedAtcCost = $remainingTons * $atc->price_per_ton;
                $this->atc_cost = (string) $calculatedAtcCost;
                
                $this->dispatch('atc-selected', [
                    'atc' => $atc,
                    'remaining_tons' => $remainingTons,
                    'total_tons' => $atc->tons,
                    'price_per_ton' => $atc->price_per_ton
                ]);
            }
        }
    }

    public function updatedTons(): void
    {
        if ($this->atc_id && (float) $this->tons > 0) {
            $atc = Atc::find($this->atc_id);
            if ($atc) {
                $allocationValidator = app(AtcAllocationValidator::class);
                $remainingTons = $allocationValidator->getRemainingTons($atc, $this->transactionId);
                
                // Auto-calculate ATC cost based on tons and price per ton
                $calculatedAtcCost = (float) $this->tons * $atc->price_per_ton;
                $this->atc_cost = (string) $calculatedAtcCost;
                
                if ((float) $this->tons > $remainingTons) {
                    $this->addError('tons', "The tons allocated ({$this->tons}) exceeds the remaining capacity ({$remainingTons}) for ATC #{$atc->atc_number}.");
                } else {
                    $this->resetErrorBag('tons');
                }
            }
        }
    }

    public function showCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }

    public function showEditForm(DailyCustomerTransaction $transaction): void
    {
        $this->transactionId = $transaction->id;
        $this->customer_id = $transaction->customer_id;
        $this->driver_id = $transaction->driver_id;
        $this->atc_id = $transaction->atc_id;
        $this->date = $transaction->date->format('Y-m-d');
        $this->origin = $transaction->origin;
        $this->deport_details = $transaction->deport_details;
        $this->cement_type = $transaction->cement_type;
        $this->destination = $transaction->destination;
        $this->atc_cost = (string) $transaction->atc_cost;
        $this->transport_cost = (string) $transaction->transport_cost;
        $this->tons = (string) $transaction->tons;
        $this->status = $transaction->status ? 'active' : 'inactive';
        
        $this->showForm = true;
        $this->isEditing = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'customer_id' => $this->customer_id,
            'driver_id' => $this->driver_id,
            'atc_id' => $this->atc_id,
            'date' => $this->date,
            'origin' => $this->origin,
            'deport_details' => $this->deport_details,
            'cement_type' => $this->cement_type,
            'destination' => $this->destination,
            'atc_cost' => (float) $this->atc_cost,
            'transport_cost' => (float) $this->transport_cost,
            'tons' => (float) $this->tons,
            'status' => $this->status,
        ];

        $transactionService = app(TransactionService::class);

        try {
            if ($this->isEditing) {
                $transaction = DailyCustomerTransaction::findOrFail($this->transactionId);
                $transactionService->updateTransaction($transaction, $data);
                $this->dispatch('success', 'Transaction updated successfully!');
            } else {
                $transactionService->createTransaction($data);
                $this->dispatch('success', 'Transaction created successfully!');
            }

            $this->resetForm();
            $this->showForm = false;
        } catch (\Exception $e) {
            $this->dispatch('error', $e->getMessage());
        }
    }

    public function delete(DailyCustomerTransaction $transaction): void
    {
        try {
            $transactionService = app(TransactionService::class);
            $transactionService->deleteTransaction($transaction);
            $this->dispatch('success', 'Transaction deleted successfully!');
        } catch (\Exception $e) {
            $this->dispatch('error', $e->getMessage());
        }
    }

    public function showAtcAllocation(Atc $atc): void
    {
        $this->selectedAtc = $atc;
        $this->showAtcAllocation = true;
    }

    public function closeAtcAllocation(): void
    {
        $this->showAtcAllocation = false;
        $this->selectedAtc = null;
    }

    public function resetForm(): void
    {
        $this->transactionId = null;
        $this->customer_id = 0;
        $this->driver_id = 0;
        $this->atc_id = 0;
        $this->date = now()->format('Y-m-d');
        $this->origin = '';
        $this->deport_details = '';
        $this->cement_type = '';
        $this->destination = '';
        $this->atc_cost = '0';
        $this->transport_cost = '0';
        $this->tons = '0';
        $this->status = 'active';
        $this->resetErrorBag();
    }

    public function getTransactionsProperty()
    {
        $request = request();
        $request->merge([
            'search' => $this->search,
            'filter' => $this->filter,
        ]);

        $transactionService = app(TransactionService::class);
        return $transactionService->getTransactionsWithAllocationInfo($request);
    }

    public function getAtcAllocationStatsProperty(): array
    {
        $transactionService = app(TransactionService::class);
        return $transactionService->getAtcAllocationStatistics();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function getDriversProperty()
    {
        return Driver::orderBy('name')->get();
    }

    public function getAtcsProperty()
    {
        return Atc::orderBy('atc_number')->get();
    }

    public function render()
    {
        return view('livewire.transaction.transaction-manager', [
            'transactions' => $this->transactions,
            'allocationStats' => $this->atcAllocationStats,
            'customers' => $this->customers,
            'drivers' => $this->drivers,
            'atcs' => $this->atcs,
        ]);
    }
}