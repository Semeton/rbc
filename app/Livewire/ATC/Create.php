<?php

declare(strict_types=1);

namespace App\Livewire\ATC;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $company = '';

    #[Validate('required|integer')]
    public int $atc_number = 0;

    #[Validate('required|in:bg,cash_payment')]
    public string $atc_type = 'bg';

    #[Validate('required|numeric|min:0')]
    public float $amount = 0.0;

    #[Validate('required|integer|min:0')]
    public int $tons = 0;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function store(): void
    {
        $this->validate();

        $atcService = app(\App\ATC\Services\ATCService::class);
        $atc = $atcService->createATC([
            'company' => $this->company,
            'atc_number' => $this->atc_number,
            'atc_type' => $this->atc_type,
            'amount' => $this->amount,
            'tons' => $this->tons,
            'status' => $this->status,
        ]);

        $this->redirect(route('atcs.show', $atc));
    }

    public function render(): View
    {
        return view('livewire.atc.create');
    }
}
