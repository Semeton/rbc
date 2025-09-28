<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Transaction Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage daily customer transactions with ATC allocation</p>
            </div>
                <flux:button 
                    href="{{ route('transactions.create') }}" 
                    variant="primary"
                >
                    <flux:icon name="plus" class="w-4 h-4 mr-2" />
                    Add Transaction
                </flux:button>
        </div>

        <!-- ATC Allocation Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <flux:icon name="truck" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total ATCs</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->atcAllocationStats['total_atcs'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <flux:icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available ATCs</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->atcAllocationStats['available_atcs'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <flux:icon name="exclamation-triangle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Fully Allocated</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->atcAllocationStats['fully_allocated_atcs'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <flux:icon name="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Over Allocated</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $this->atcAllocationStats['over_allocated_atcs'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search transactions..." 
                        class="w-full"
                    />
                </div>
                <div class="md:w-48">
                    <flux:select wire:model.live="filter" class="w-full">
                        <option value="all">All Transactions</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Transaction Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                ATC & Allocation
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Remaining Capacity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Costs
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $transaction->customer->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->driver->name }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $transaction->date->format('M d, Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            ATC #{{ $transaction->atc->atc_number }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ number_format($transaction->tons, 2) }} tons
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ number_format($transaction->atc->remaining_tons, 2) }} tons left
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            ₦{{ number_format($transaction->atc->remaining_amount, 2) }} left
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ number_format($transaction->atc->allocation_percentage, 1) }}% allocated
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <div>
                                        <div class="font-medium">₦{{ number_format($transaction->atc_cost, 2) }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            + ₦{{ number_format($transaction->transport_cost, 2) }} transport
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                        {{ $transaction->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                            <flux:button variant="outline" size="sm" :href="route('transactions.show', $transaction)">
                                                <flux:icon name="eye" class="h-4 w-4" />
                                            </flux:button>
                                        
                                            <flux:button variant="outline" size="sm" :href="route('transactions.edit', $transaction)">
                                                <flux:icon name="pencil" class="h-4 w-4" />
                                            </flux:button>
                                            <flux:button 
                                                variant="outline" 
                                                size="sm" 
                                                wire:click="deleteTransaction({{ $transaction->id }})"
                                                wire:confirm="Are you sure you want to delete this transaction? This action cannot be undone."
                                                class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20"
                                            >
                                                <flux:icon name="trash" class="h-4 w-4" />
                                            </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
</div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->transactions->links() }}
            </div>
        </div>
    </div>
</div>