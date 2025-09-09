<?php

declare(strict_types=1);

namespace App\Transaction\Controllers;

use App\Models\DailyCustomerTransaction;
use App\Transaction\Requests\StoreTransactionRequest;
use App\Transaction\Requests\UpdateTransactionRequest;
use App\Transaction\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function index(): View
    {
        return view('transactions.index');
    }

    public function create(): View
    {
        return view('transactions.create');
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(DailyCustomerTransaction $transaction): View
    {
        return view('transactions.show', compact('transaction'));
    }

    public function edit(DailyCustomerTransaction $transaction): View
    {
        return view('transactions.edit', compact('transaction'));
    }

    public function update(UpdateTransactionRequest $request, DailyCustomerTransaction $transaction): RedirectResponse
    {
        $this->transactionService->updateTransaction($transaction, $request->validated());

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(DailyCustomerTransaction $transaction): RedirectResponse
    {
        $this->transactionService->deleteTransaction($transaction);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}
