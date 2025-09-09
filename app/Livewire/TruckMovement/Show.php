<?php

declare(strict_types=1);

namespace App\Livewire\TruckMovement;

use App\Models\DailyTruckRecord;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public DailyTruckRecord $truckMovement;

    public function mount(DailyTruckRecord $truckMovement): void
    {
        $this->truckMovement = $truckMovement;
    }

    public function render(): View
    {
        return view('livewire.truck-movement.show');
    }
}
