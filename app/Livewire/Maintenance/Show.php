<?php

declare(strict_types=1);

namespace App\Livewire\Maintenance;

use App\Models\TruckMaintenanceRecord;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public TruckMaintenanceRecord $maintenance;

    public function mount(TruckMaintenanceRecord $maintenance): void
    {
        $this->maintenance = $maintenance;
    }

    public function render(): View
    {
        return view('livewire.maintenance.show');
    }
}
