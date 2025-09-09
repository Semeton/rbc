<?php

declare(strict_types=1);

namespace App\TruckMovement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTruckMovementRequest extends FormRequest
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
            'driver_id' => 'required|exists:drivers,id',
            'truck_id' => 'required|exists:trucks,id',
            'customer_id' => 'required|exists:customers,id',
            'atc_collection_date' => 'required|date',
            'load_dispatch_date' => 'required|date|after_or_equal:atc_collection_date',
            'fare' => 'required|numeric|min:0',
            'gas_chop_money' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'driver_id.required' => 'Please select a driver.',
            'driver_id.exists' => 'The selected driver does not exist.',
            'truck_id.required' => 'Please select a truck.',
            'truck_id.exists' => 'The selected truck does not exist.',
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'atc_collection_date.required' => 'ATC collection date is required.',
            'atc_collection_date.date' => 'Please enter a valid ATC collection date.',
            'load_dispatch_date.required' => 'Load dispatch date is required.',
            'load_dispatch_date.date' => 'Please enter a valid load dispatch date.',
            'load_dispatch_date.after_or_equal' => 'Load dispatch date must be after or equal to ATC collection date.',
            'fare.required' => 'Fare amount is required.',
            'fare.numeric' => 'Fare amount must be a valid number.',
            'fare.min' => 'Fare amount must be greater than or equal to 0.',
            'gas_chop_money.required' => 'Gas chop money is required.',
            'gas_chop_money.numeric' => 'Gas chop money must be a valid number.',
            'gas_chop_money.min' => 'Gas chop money must be greater than or equal to 0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
