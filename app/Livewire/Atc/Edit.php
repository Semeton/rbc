<?php

declare(strict_types=1);

namespace App\Livewire\ATC;

use App\Models\Atc;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Atc $atc;

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

    public function mount(Atc $atc): void
    {
        $this->atc = $atc;
        $this->company = $atc->company;
        $this->atc_number = $atc->atc_number;
        $this->atc_type = $atc->getRawOriginal('atc_type');
        $this->amount = $atc->amount;
        $this->tons = $atc->tons;
        $this->status = $atc->status_string;
    }

    public function update(): void
    {
        $this->validate();

        $atcService = app(\App\ATC\Services\ATCService::class);
        $atcService->updateATC($this->atc, [
            'company' => $this->company,
            'atc_number' => $this->atc_number,
            'atc_type' => $this->atc_type,
            'amount' => $this->amount,
            'tons' => $this->tons,
            'status' => $this->status,
        ]);

        $this->redirect(route('atcs.show', $this->atc));
    }

    public function render(): View
    {
        return view('livewire.atc.edit');
    }
}
