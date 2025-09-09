<?php

declare(strict_types=1);

namespace App\Payment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'customer_id' => 'required|exists:customers,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0.01',
            'bank_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.date' => 'Please enter a valid payment date.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
            'amount.required' => 'Payment amount is required.',
            'amount.numeric' => 'Payment amount must be a valid number.',
            'amount.min' => 'Payment amount must be greater than 0.',
            'bank_name.max' => 'Bank name cannot exceed 255 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
}
