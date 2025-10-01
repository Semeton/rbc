<?php

declare(strict_types=1);

namespace App\ATC\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateATCRequest extends FormRequest
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
            'company' => ['required', 'in:Dangote,BUA,Mangal'],
            'atc_number' => [
                'required',
                'integer',
                // TODO: Add unique validation ignoring current ATC when route binding is properly set up
            ],
            'atc_type' => ['required', 'in:bg,cash_payment'],
            'amount' => ['required', 'numeric', 'min:0'],
            'tons' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company.required' => 'Company is required.',
            'company.in' => 'Company must be Dangote, BUA, or Mangal.',
            'atc_number.required' => 'ATC number is required.',
            'atc_number.integer' => 'ATC number must be a valid number.',
            'atc_number.unique' => 'This ATC number is already in use.',
            'atc_type.required' => 'ATC type is required.',
            'atc_type.in' => 'ATC type must be either BG or Cash Payment.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be greater than or equal to 0.',
            'tons.required' => 'Tons is required.',
            'tons.integer' => 'Tons must be a valid number.',
            'tons.min' => 'Tons must be greater than or equal to 0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
