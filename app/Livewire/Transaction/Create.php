<?php

declare(strict_types=1);

namespace App\Livewire\Transaction;

use App\Models\Atc;
use App\Models\Customer;
use App\Models\Driver;
use App\Services\AtcAllocationValidator;
use App\Services\AuditTrailService;
use App\Transaction\Services\TransactionService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    // Form fields
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

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'driver_id' => 'required|exists:drivers,id',
        'atc_id' => 'required|exists:atcs,id',
        'date' => 'required|date',
        'origin' => 'required|string|max:255',
        'deport_details' => 'nullable|string|max:500',
        'cement_type' => 'required|string|max:100',
        'destination' => 'required|string|max:255',
        // Rename in UI to "Cost" but persist to atc_cost
        'atc_cost' => 'required|numeric|min:0',
        // Transport cost removed from UI; keep optional for backward compatibility
        'transport_cost' => 'nullable|numeric|min:0',
        'tons' => 'required|numeric|min:0.01',
        'status' => 'required|in:active,inactive',
    ];

    protected function rules()
    {
        // Use base rules; no auto-calculation enforcement for atc_cost
        return $this->rules;
    }

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
        AuditTrailService::log('page_view', 'Transactions', 'Viewed transaction create page');
    }

    public function updatedAtcId(): void
    {
        if ($this->atc_id) {
            $atc = Atc::find($this->atc_id);
            if ($atc) {
                $allocationValidator = app(AtcAllocationValidator::class);
                $remainingTons = $allocationValidator->getRemainingTons($atc);
                // Do not auto-fill tons or cost; staff will enter manually
                $this->dispatch('atc-selected', [
                    'atc' => $atc,
                    'remaining_tons' => $remainingTons,
                    'total_tons' => $atc->tons,
                    'price_per_ton' => $atc->price_per_ton,
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
                $remainingTons = $allocationValidator->getRemainingTons($atc);
                if ((float) $this->tons > $remainingTons) {
                    $this->addError('tons', "The tons allocated ({$this->tons}) exceeds the remaining capacity ({$remainingTons}) for ATC #{$atc->atc_number}.");
                } else {
                    $this->resetErrorBag('tons');
                }
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        // Normalize optional text fields
        $normalizedDeportDetails = trim($this->deport_details) === '' ? null : $this->deport_details;

        // Normalize transport_cost to null if empty string for backward compatibility
        $normalizedTransportCost = trim((string) $this->transport_cost) === '' ? null : (float) $this->transport_cost;

        $data = [
            'customer_id' => $this->customer_id,
            'driver_id' => $this->driver_id,
            'atc_id' => $this->atc_id,
            'date' => $this->date,
            'origin' => $this->origin,
            'deport_details' => $normalizedDeportDetails,
            'cement_type' => $this->cement_type,
            'destination' => $this->destination,
            'atc_cost' => (float) $this->atc_cost,
            'transport_cost' => $normalizedTransportCost,
            'tons' => (float) $this->tons,
            'status' => $this->status,
        ];

        $transactionService = app(TransactionService::class);

        try {
            $transactionService->createTransaction($data);
            $this->dispatch('success', 'Transaction created successfully!');

            // Redirect to transactions index
            $this->redirect(route('transactions.index'));
        } catch (\Exception $e) {
            $this->dispatch('error', $e->getMessage());
        }
    }

    public function resetForm(): void
    {
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

    public function render(): View
    {
        return view('livewire.transaction.create');
    }
}
