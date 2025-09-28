<x-layouts.app title="Transaction Details">
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
                            @can('update', $transaction)
                                <flux:button variant="outline" :href="route('transactions.edit', $transaction)">
                                    <flux:icon name="pencil" class="h-4 w-4" />
                                    Edit
                                </flux:button>
                            @endcan
                            <flux:button variant="outline" :href="route('transactions.index')">
                                <flux:icon name="arrow-left" class="h-4 w-4" />
                                Back to Transactions
                            </flux:button>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Basic Information</h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->customer->name }}</dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Driver</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->driver->name }}</dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->date->format('M d, Y') }}</dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Origin</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->origin }}</dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Destination</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->destination }}</dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cement Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->cement_type }}</dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tons</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($transaction->tons, 2) }} tons</dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                        {{ $transaction->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $transaction->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- ATC Information -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">ATC Information</h4>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <h5 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                                        ATC #{{ $transaction->atc->atc_number }} - {{ $transaction->atc->company }}
                                    </h5>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">ATC Type:</span>
                                            <span class="text-sm text-blue-900 dark:text-blue-100">{{ $transaction->atc->atc_type_display }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Capacity:</span>
                                            <span class="text-sm text-blue-900 dark:text-blue-100">{{ number_format($transaction->atc->tons, 2) }} tons</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Value:</span>
                                            <span class="text-sm text-blue-900 dark:text-blue-100">₦{{ number_format($transaction->atc->amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Price per Ton:</span>
                                            <span class="text-sm text-blue-900 dark:text-blue-100">₦{{ number_format($transaction->atc->price_per_ton, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Allocation Status</h5>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">This Transaction:</span>
                                            <span class="text-sm text-blue-900 dark:text-blue-100">{{ number_format($transaction->tons, 2) }} tons</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Already Allocated:</span>
                                            <span class="text-sm text-blue-900 dark:text-blue-100">{{ number_format($transaction->atc->allocated_tons, 2) }} tons</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-green-700 dark:text-green-400">Available:</span>
                                            <span class="text-sm text-green-900 dark:text-green-100">{{ number_format($transaction->atc->remaining_tons, 2) }} tons</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Utilization:</span>
                                            <span class="text-sm text-blue-900 dark:text-blue-100">{{ number_format($transaction->atc->allocation_percentage, 1) }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                                    <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full" style="width: {{ $transaction->atc->allocation_percentage }}%"></div>
                                </div>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2 text-center">{{ number_format($transaction->atc->allocation_percentage, 1) }}% of ATC capacity utilized</p>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Financial Information</h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                                <dt class="text-sm font-medium text-green-700 dark:text-green-400">ATC Cost</dt>
                                <dd class="mt-1 text-lg font-semibold text-green-900 dark:text-green-100">₦{{ number_format($transaction->atc_cost, 2) }}</dd>
                                <dd class="text-xs text-green-600 dark:text-green-500 mt-1">{{ number_format($transaction->atc_cost / $transaction->tons, 2) }} per ton</dd>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                <dt class="text-sm font-medium text-blue-700 dark:text-blue-400">Transport Cost</dt>
                                <dd class="mt-1 text-lg font-semibold text-blue-900 dark:text-blue-100">₦{{ number_format($transaction->transport_cost, 2) }}</dd>
                                <dd class="text-xs text-blue-600 dark:text-blue-500 mt-1">{{ number_format($transaction->transport_cost / $transaction->tons, 2) }} per ton</dd>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                                <dt class="text-sm font-medium text-purple-700 dark:text-purple-400">Total Cost</dt>
                                <dd class="mt-1 text-lg font-semibold text-purple-900 dark:text-purple-100">₦{{ number_format($transaction->total_cost, 2) }}</dd>
                                <dd class="text-xs text-purple-600 dark:text-purple-500 mt-1">{{ number_format($transaction->total_cost / $transaction->tons, 2) }} per ton</dd>
                            </div>

                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-800">
                                <dt class="text-sm font-medium text-orange-700 dark:text-orange-400">Total Tons</dt>
                                <dd class="mt-1 text-lg font-semibold text-orange-900 dark:text-orange-100">{{ number_format($transaction->tons, 2) }}</dd>
                                <dd class="text-xs text-orange-600 dark:text-orange-500 mt-1">tons allocated</dd>
                            </div>
                        </div>
                    </div>

                    @if($transaction->deport_details)
                        <div class="mt-8">
                            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Deport Details</h4>
                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $transaction->deport_details }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
