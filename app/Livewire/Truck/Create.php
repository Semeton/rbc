<?php

declare(strict_types=1);

namespace App\Livewire\Truck;

use App\Models\Truck as TruckModel;
use App\Truck\Services\TruckService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('nullable|string|max:50')]
    public string $cab_number = '';

    #[Validate('required|string|max:20')]
    public string $registration_number = '';

    #[Validate('nullable|string|max:100')]
    public string $truck_model = '';

    #[Validate('nullable|integer|min:1900|max:'.(2025))]
    public int $year_of_manufacture = 2020;

    #[Validate('nullable|in:active,inactive')]
    public string $status = 'active';

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    public function rules(): array
    {
        return [
            'cab_number' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['required', 'string', 'max:20', 'unique:trucks'],
            'truck_model' => ['nullable', 'string', 'max:100'],
            'year_of_manufacture' => ['nullable', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'status' => ['nullable', 'in:active,inactive'],
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

        $truck = app(TruckService::class)->createTruck($data);

        $this->dispatch('truck-created', $truck->id);

        $this->redirect(route('trucks.show', $truck), navigate: true);
    }

    #[Computed]
    public function recentTrucks()
    {
        return TruckModel::latest()->limit(5)->get();
    }

    public function render(): View
    {
        return view('livewire.truck.create');
    }
}
