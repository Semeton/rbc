<?php

declare(strict_types=1);

namespace App\Livewire\Payment;

use App\Models\Customer;
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
    public ?int $customer_id = null;

    #[Url]
    public string $bank_name = '';

    #[Url]
    public string $date_from = '';

    #[Url]
    public string $date_to = '';

    #[Url]
    public ?float $amount_min = null;

    #[Url]
    public ?float $amount_max = null;

    #[Url]
    public int $perPage = 15;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCustomerId(): void
    {
        $this->resetPage();
    }

    public function updatedBankName(): void
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

    public function updatedAmountMin(): void
    {
        $this->resetPage();
    }

    public function updatedAmountMax(): void
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
        $this->customer_id = null;
        $this->bank_name = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->amount_min = null;
        $this->amount_max = null;
        $this->perPage = 15;
        $this->resetPage();
    }

    #[Computed]
    public function payments()
    {
        $request = app(Request::class);
        $request->merge([
            'search' => $this->search,
            'customer_id' => $this->customer_id,
            'bank_name' => $this->bank_name,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'amount_min' => $this->amount_min,
            'amount_max' => $this->amount_max,
        ]);

        return app(\App\Payment\Services\PaymentService::class)->getPaginatedPayments($request, $this->perPage);
    }

    #[Computed]
    public function statistics(): array
    {
        return app(\App\Payment\Services\PaymentService::class)->getPaymentStatistics();
    }

    #[Computed]
    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.payment.index');
    }
}
