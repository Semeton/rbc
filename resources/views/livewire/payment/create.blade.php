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
                            <flux:select wire:model="customer_id" searchable>
                                <flux:select.option value="">Select a customer...</flux:select.option>
                                @foreach($this->customers as $customer)
                                    <flux:select.option value="{{ $customer->id }}">{{ $customer->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
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
                                min="0.01"
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
</div>