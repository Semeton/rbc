<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Payment Details</flux:heading>
                <flux:subheading>View payment information</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button variant="primary" :href="route('payments.edit', $payment)" wire:navigate>
                    <flux:icon name="pencil" />
                    Edit Payment
                </flux:button>
                <flux:button variant="outline" :href="route('payments.index')" wire:navigate>
                    <flux:icon name="arrow-left" />
                    Back to Payments
                </flux:button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Payment Information -->
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Payment Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Payment Amount</dt>
                            <dd class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">₵{{ $payment->formatted_amount }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Payment Date</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $payment->payment_date->format('F d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Bank Name</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $payment->bank_name ?? 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Created</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $payment->created_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    </dl>

                    @if($payment->notes)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Notes</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 whitespace-pre-wrap">{{ $payment->notes }}</dd>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div>
            <div class="rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Customer Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <flux:avatar :name="$payment->customer->name" size="lg" />
                        <div>
                            <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $payment->customer->name }}</h4>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $payment->customer->email }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <flux:button variant="outline" size="sm" :href="route('customers.show', $payment->customer)" wire:navigate>
                            <flux:icon name="eye" />
                            View Customer
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <flux:button variant="outline" size="sm" :href="route('payments.edit', $payment)" wire:navigate class="w-full">
                            <flux:icon name="pencil" />
                            Edit Payment
                        </flux:button>
                        <flux:button variant="outline" size="sm" :href="route('customers.show', $payment->customer)" wire:navigate class="w-full">
                            <flux:icon name="user" />
                            View Customer
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>