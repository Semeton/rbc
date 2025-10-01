<?php

declare(strict_types=1);

namespace App\Livewire\Driver;

use App\Driver\Services\DriverService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|image|mimes:jpeg,png,jpg,gif|max:2048')]
    public ?UploadedFile $photo = null;

    #[Validate('nullable|string|max:255')]
    public string $company = '';

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function __construct()
    {
        // Livewire components don't support constructor dependency injection
        // We'll use the service directly in methods
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'company' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Driver name is required.',
            'name.max' => 'Driver name cannot exceed 255 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'photo.image' => 'The photo must be an image.',
            'photo.mimes' => 'The photo must be a file of type: jpeg, png, jpg, gif.',
            'photo.max' => 'The photo may not be greater than 2MB.',
            'company.max' => 'Company name cannot exceed 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'company' => $this->company,
            'status' => $this->status,
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo;
        }

        $driver = app(DriverService::class)->createDriver($data);

        $this->dispatch('driver-created', $driver->id);

        $this->redirect(route('drivers.show', $driver), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.driver.create');
    }
}
