<?php

declare(strict_types=1);

namespace App\Livewire\Maintenance;

use App\Models\Truck;
use App\Models\TruckMaintenanceRecord;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public TruckMaintenanceRecord $maintenance;

    #[Validate('required|exists:trucks,id')]
    public int $truck_id = 0;

    #[Validate('required|string|max:1000')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public float $cost_of_maintenance = 0.0;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function mount(TruckMaintenanceRecord $maintenance): void
    {
        $this->maintenance = $maintenance;
        $this->truck_id = $maintenance->truck_id;
        $this->description = $maintenance->description;
        $this->cost_of_maintenance = (float) $maintenance->cost_of_maintenance;
        $this->status = $maintenance->status_string;
    }

    public function update(): void
    {
        $this->validate();

        app(\App\Maintenance\Services\MaintenanceService::class)->updateMaintenance($this->maintenance, [
            'truck_id' => $this->truck_id,
            'description' => $this->description,
            'cost_of_maintenance' => $this->cost_of_maintenance,
            'status' => $this->status === 'active',
        ]);

        $this->redirect(route('maintenance.show', $this->maintenance));
    }

    #[Computed]
    public function trucks()
    {
        return Truck::orderBy('registration_number')->get();
    }

    public function render(): View
    {
        return view('livewire.maintenance.edit');
    }
}
