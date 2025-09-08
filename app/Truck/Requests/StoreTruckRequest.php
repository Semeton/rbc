<?php

declare(strict_types=1);

namespace App\Truck\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTruckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implement proper authorization policies
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cab_number' => ['required', 'string', 'max:50'],
            'registration_number' => ['required', 'string', 'max:20', 'unique:trucks'],
            'truck_model' => ['required', 'string', 'max:100'],
            'year_of_manufacture' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
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
}
