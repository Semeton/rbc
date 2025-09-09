<div>
    <x-breadcrumb :items="[
        ['name' => 'Daily Transactions', 'url' => route('transactions.index')],
        ['name' => 'Transaction Details', 'url' => '#']
    ]" />

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                            Transaction Details - {{ $transaction->date->format('M d, Y') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Created {{ $transaction->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="outline" :href="route('transactions.edit', $transaction)" wire:navigate>
                            <flux:icon name="pencil" class="h-4 w-4" />
                            Edit
                        </flux:button>
                        <flux:button variant="outline" :href="route('transactions.index')" wire:navigate>
                            <flux:icon name="arrow-left" class="h-4 w-4" />
                            Back to Transactions
                        </flux:button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->customer->name }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Driver</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->driver->name }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ATC</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">ATC #{{ $transaction->atc->atc_number }} - {{ $transaction->atc->company }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->date->format('M d, Y') }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Origin</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->origin }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Destination</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->destination }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cement Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->cement_type }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$transaction->status_string" />
                        </dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="mt-8">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Financial Information</h4>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ATC Cost</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($transaction->atc_cost, 2) }}</dd>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Transport Cost</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($transaction->transport_cost, 2) }}</dd>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cost</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($transaction->total_cost, 2) }}</dd>
                        </div>
                    </div>
                </div>

                @if($transaction->deport_details)
                    <div class="mt-8">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Deport Details</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $transaction->deport_details }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>