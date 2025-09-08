<?php

declare(strict_types=1);

namespace App\Livewire\Truck;

use App\Models\Truck;
use App\Truck\Services\TruckService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Truck $truck;

    #[Validate('required|string|max:50')]
    public string $cab_number = '';

    #[Validate('required|string|max:20')]
    public string $registration_number = '';

    #[Validate('required|string|max:100')]
    public string $truck_model = '';

    #[Validate('required|integer|min:1900|max:'.(2025))]
    public int $year_of_manufacture = 2020;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    public function mount(Truck $truck): void
    {
        $this->truck = $truck;
        $this->cab_number = $truck->cab_number ?? '';
        $this->registration_number = $truck->registration_number ?? '';
        $this->truck_model = $truck->truck_model ?? '';
        $this->year_of_manufacture = $truck->year_of_manufacture ?? 2020;
        $this->status = $truck->status_string ?? 'active';
    }

    public function rules(): array
    {
        return [
            'cab_number' => ['required', 'string', 'max:50'],
            'registration_number' => ['required', 'string', 'max:20', 'unique:trucks,registration_number,'.$this->truck->id],
            'truck_model' => ['required', 'string', 'max:100'],
            'year_of_manufacture' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'cab_number.required' => 'Cab number is required.',
            'cab_number.max' => 'Cab number cannot exceed 50 characters.',
            'registration_number.required' => 'Registration number is required.',
            'registration_number.max' => 'Registration number cannot exceed 20 characters.',
            'registration_number.unique' => 'This registration number is already in use.',
            'truck_model.required' => 'Truck model is required.',
            'truck_model.max' => 'Truck model cannot exceed 100 characters.',
            'year_of_manufacture.required' => 'Year of manufacture is required.',
            'year_of_manufacture.integer' => 'Year of manufacture must be a valid year.',
            'year_of_manufacture.min' => 'Year of manufacture must be after 1900.',
            'year_of_manufacture.max' => 'Year of manufacture cannot be in the future.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'cab_number' => $this->cab_number,
            'registration_number' => $this->registration_number,
            'truck_model' => $this->truck_model,
            'year_of_manufacture' => $this->year_of_manufacture,
            'status' => $this->status,
        ];

        app(TruckService::class)->updateTruck($this->truck, $data);

        $this->dispatch('truck-updated', $this->truck->id);

        $this->redirect(route('trucks.show', $this->truck), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.truck.edit');
    }
}
