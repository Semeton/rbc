<?php

declare(strict_types=1);

namespace App\Livewire\Payment;

use App\Models\CustomerPayment;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public CustomerPayment $payment;

    public function mount(CustomerPayment $payment): void
    {
        $this->payment = $payment;
    }

    public function render(): View
    {
        return view('livewire.payment.show');
    }
}
