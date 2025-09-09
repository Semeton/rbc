<?php

declare(strict_types=1);

namespace App\Payment\Controllers;

use App\Models\CustomerPayment;
use App\Payment\Requests\StorePaymentRequest;
use App\Payment\Requests\UpdatePaymentRequest;
use App\Payment\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}

    /**
     * Display a listing of payments
     */
    public function index(): View
    {
        return view('payments.index');
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(): View
    {
        return view('payments.create');
    }

    /**
     * Store a newly created payment
     */
    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->paymentService->createPayment($request->validated());

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Payment created successfully.');
    }

    /**
     * Display the specified payment
     */
    public function show(CustomerPayment $payment): View
    {
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit(CustomerPayment $payment): View
    {
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment
     */
    public function update(UpdatePaymentRequest $request, CustomerPayment $payment): RedirectResponse
    {
        $this->paymentService->updatePayment($payment, $request->validated());

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment from storage
     */
    public function destroy(CustomerPayment $payment): RedirectResponse
    {
        $this->paymentService->deletePayment($payment);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
