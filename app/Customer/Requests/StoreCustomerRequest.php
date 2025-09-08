<?php

declare(strict_types=1);

namespace App\Customer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For now, allow all authenticated users to create customers
        // TODO: Implement proper authorization policy when user roles are defined
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'name.max' => 'Customer name cannot exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'address.required' => 'Address is required.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'city.required' => 'City is required.',
            'city.max' => 'City name cannot exceed 100 characters.',
            'state.required' => 'State is required.',
            'state.max' => 'State name cannot exceed 100 characters.',
            'zip_code.required' => 'ZIP code is required.',
            'zip_code.max' => 'ZIP code cannot exceed 20 characters.',
            'country.required' => 'Country is required.',
            'country.max' => 'Country name cannot exceed 100 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
