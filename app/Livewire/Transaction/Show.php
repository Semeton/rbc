<?php

declare(strict_types=1);

namespace App\Livewire\Transaction;

use App\Models\DailyCustomerTransaction;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public DailyCustomerTransaction $transaction;

    public function render(): View
    {
        return view('livewire.transaction.show');
    }
}
