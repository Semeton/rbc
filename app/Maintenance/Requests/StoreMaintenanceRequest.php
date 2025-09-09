<?php

declare(strict_types=1);

namespace App\Maintenance\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        // TODO: Implement proper authorization policy
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            'truck_id' => 'required|exists:trucks,id',
            'description' => 'required|string|max:1000',
            'cost_of_maintenance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'truck_id.required' => 'Please select a truck.',
            'truck_id.exists' => 'The selected truck does not exist.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a valid text.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'cost_of_maintenance.required' => 'Cost of maintenance is required.',
            'cost_of_maintenance.numeric' => 'Cost of maintenance must be a valid number.',
            'cost_of_maintenance.min' => 'Cost of maintenance must be greater than or equal to 0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
