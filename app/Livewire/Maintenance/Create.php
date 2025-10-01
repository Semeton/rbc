<?php

declare(strict_types=1);

namespace App\Livewire\Maintenance;

use App\Models\Truck;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|exists:trucks,id')]
    public int $truck_id = 0;

    #[Validate('required|string|max:1000')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public float $cost_of_maintenance = 0.0;

    #[Validate('required|date')]
    public string $maintenance_date = '';

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function store(): void
    {
        $this->validate();

        $maintenance = app(\App\Maintenance\Services\MaintenanceService::class)->createMaintenance([
            'truck_id' => $this->truck_id,
            'description' => $this->description,
            'cost_of_maintenance' => $this->cost_of_maintenance,
            'maintenance_date' => $this->maintenance_date,
            'status' => $this->status === 'active',
        ]);

        $this->redirect(route('maintenance.show', $maintenance));
    }

    #[Computed]
    public function trucks()
    {
        return Truck::orderBy('registration_number')->get();
    }

    public function render(): View
    {
        return view('livewire.maintenance.create');
    }
}
