<?php

declare(strict_types=1);

namespace App\Livewire\Truck;

use App\Models\Truck;
use App\Truck\Services\TruckService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Truck $truck;

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    public function mount(Truck $truck): void
    {
        $this->truck = $truck;
    }

    public function deleteTruck(): void
    {
        app(TruckService::class)->deleteTruck($this->truck);

        $this->dispatch('truck-deleted');

        $this->redirect(route('trucks.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.truck.show');
    }
}
