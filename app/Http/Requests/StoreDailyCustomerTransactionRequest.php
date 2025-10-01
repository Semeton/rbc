<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Atc;
use App\Services\AtcAllocationValidator;
use Illuminate\Foundation\Http\FormRequest;

class StoreDailyCustomerTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'atc_id' => ['required', 'exists:atcs,id'],
            'date' => ['required', 'date'],
            'origin' => ['required', 'string', 'max:255'],
            'deport_details' => ['nullable', 'string', 'max:255'],
            'cement_type' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'atc_cost' => ['required', 'numeric', 'min:0'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'],
            'tons' => ['required', 'numeric', 'min:0.01'],
            'status' => ['boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateAtcAllocation($validator);
        });
    }

    /**
     * Validate ATC allocation
     */
    private function validateAtcAllocation($validator): void
    {
        $atcId = $this->input('atc_id');
        $tons = (float) $this->input('tons');
        $transactionId = $this->route('transaction') ?? null;

        if (! $atcId || ! $tons) {
            return;
        }

        $atc = Atc::find($atcId);
        if (! $atc) {
            return;
        }

        $allocationValidator = app(AtcAllocationValidator::class);
        $remainingTons = $allocationValidator->getRemainingTons($atc, $transactionId);

        if ($tons > $remainingTons) {
            $validator->errors()->add('tons',
                "The tons allocated ({$tons}) exceeds the remaining capacity ({$remainingTons}) for ATC #{$atc->atc_number}."
            );
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'driver_id.required' => 'Please select a driver.',
            'driver_id.exists' => 'The selected driver does not exist.',
            'atc_id.required' => 'Please select an ATC.',
            'atc_id.exists' => 'The selected ATC does not exist.',
            'date.required' => 'Please select a date.',
            'date.date' => 'Please enter a valid date.',
            'origin.required' => 'Please enter the origin.',
            'cement_type.required' => 'Please enter the cement type.',
            'destination.required' => 'Please enter the destination.',
            'atc_cost.required' => 'Please enter the ATC cost.',
            'atc_cost.numeric' => 'ATC cost must be a number.',
            'atc_cost.min' => 'ATC cost must be at least 0.',
            'transport_cost.numeric' => 'Transport cost must be a number.',
            'transport_cost.min' => 'Transport cost must be at least 0.',
            'tons.required' => 'Please enter the number of tons.',
            'tons.numeric' => 'Tons must be a number.',
            'tons.min' => 'Tons must be at least 0.01.',
        ];
    }
}
