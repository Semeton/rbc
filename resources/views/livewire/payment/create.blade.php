<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Create Payment</flux:heading>
                <flux:subheading>Record a new customer payment</flux:subheading>
            </div>
            <flux:button variant="outline" :href="route('payments.index')" wire:navigate>
                <flux:icon name="arrow-left" />
                Back to Payments
            </flux:button>
        </div>
    </div>

    <div class="max-w-2xl">
        <form wire:submit="store" class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="space-y-6">
                    <div>
                        <flux:field>
                            <flux:label>Customer</flux:label>
                            <select wire:model="customer_id" class="select2-customer w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select a customer...</option>
                                @foreach($this->customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <flux:error name="customer_id" />
                        </flux:field>
                    </div>

                    <div>
                        <flux:field>
                            <flux:label>Payment Date</flux:label>
                            <flux:input
                                type="date"
                                wire:model="payment_date"
                            />
                            <flux:error name="payment_date" />
                        </flux:field>
                    </div>

                    <div>
                        <flux:field>
                            <flux:label>Amount</flux:label>
                            <flux:input
                                type="number"
                                step="0.01"
                                {{-- min="0.01" --}}
                                wire:model="amount"
                                placeholder="Enter payment amount..."
                            />
                            <flux:error name="amount" />
                        </flux:field>
                    </div>

                    <div>
                        <flux:field>
                            <flux:label>Bank Name</flux:label>
                            <flux:input
                                wire:model="bank_name"
                                placeholder="Enter bank name (optional)..."
                            />
                            <flux:error name="bank_name" />
                        </flux:field>
                    </div>

                    <div>
                        <flux:field>
                            <flux:label>Notes</flux:label>
                            <flux:textarea
                                wire:model="notes"
                                placeholder="Enter any additional notes (optional)..."
                                rows="3"
                            />
                            <flux:error name="notes" />
                        </flux:field>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <flux:button variant="outline" :href="route('payments.index')" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Create Payment</span>
                    <span wire:loading>Creating...</span>
                </flux:button>
            </div>
        </form>
    </div>

    <div class="mt-10">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Latest Payments</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Payment Date</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Amount (₦)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Bank</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->recentPayments as $payment)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $payment->customer->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">₦{{ number_format($payment->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $payment->bank_name ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>