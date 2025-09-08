<?php

declare(strict_types=1);

namespace App\Livewire\Driver;

use App\Driver\Services\DriverService;
use App\Models\Driver;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Driver $driver;

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    public function mount(Driver $driver): void
    {
        $this->driver = $driver;
    }

    public function deleteDriver(): void
    {
        app(DriverService::class)->deleteDriver($this->driver);

        $this->dispatch('driver-deleted');

        $this->redirect(route('drivers.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.driver.show');
    }
}
