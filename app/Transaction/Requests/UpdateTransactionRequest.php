<?php

declare(strict_types=1);

namespace App\Transaction\Requests;

use App\Models\Atc;
use App\Services\AtcAllocationValidator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement proper authorization policies
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'atc_id' => ['required', 'exists:atcs,id'],
            'date' => ['required', 'date'],
            'origin' => ['required', 'string', 'max:255'],
            'deport_details' => ['nullable', 'string', 'max:500'],
            'cement_type' => ['required', 'string', 'max:100'],
            'destination' => ['required', 'string', 'max:255'],
            'atc_cost' => ['required', 'numeric', 'min:0'],
            'transport_cost' => ['required', 'numeric', 'min:0'],
            'tons' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

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
            'origin.max' => 'The origin must not exceed 255 characters.',
            'deport_details.max' => 'The deport details must not exceed 500 characters.',
            'cement_type.required' => 'Please enter the cement type.',
            'cement_type.max' => 'The cement type must not exceed 100 characters.',
            'destination.required' => 'Please enter the destination.',
            'destination.max' => 'The destination must not exceed 255 characters.',
            'atc_cost.required' => 'Please enter the ATC cost.',
            'atc_cost.numeric' => 'The ATC cost must be a number.',
            'atc_cost.min' => 'The ATC cost must be at least 0.',
            'transport_cost.required' => 'Please enter the transport cost.',
            'transport_cost.numeric' => 'The transport cost must be a number.',
            'transport_cost.min' => 'The transport cost must be at least 0.',
            'tons.required' => 'Please enter the number of tons.',
            'tons.numeric' => 'Tons must be a number.',
            'tons.min' => 'Tons must be at least 0.01.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The status must be either active or inactive.',
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
        $transactionId = $this->route('transaction')?->id;

        if (!$atcId || !$tons) {
            return;
        }

        $atc = Atc::find($atcId);
        if (!$atc) {
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
}
