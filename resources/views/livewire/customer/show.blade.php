<div>
    <!-- Customer Show Content -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                <span class="text-xl font-medium text-gray-700 dark:text-gray-300">
                                    {{ $customer->initials }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ $customer->email }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <x-status-badge :status="$customer->status_string" />
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Member since {{ $customer->created_at ? $customer->created_at->format('M Y') : 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <flux:button variant="outline" href="{{ route('customers.edit', $customer) }}">
                                <flux:icon name="pencil" class="w-4 h-4 mr-2" />
                                Edit
                            </flux:button>
                            <flux:button 
                                variant="outline" 
                                wire:click="deleteCustomer"
                                wire:confirm="Are you sure you want to delete this customer? This action cannot be undone."
                            >
                                <flux:icon name="trash" class="w-4 h-4 mr-2" />
                                Delete
                            </flux:button>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Contact Information -->
                        <div class="lg:col-span-2">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->email }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->phone }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Summary -->
                        <div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Summary</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Current Balance</label>
                                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                                            ${{ number_format($customer->balance, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Transactions</label>
                                        <p class="mt-1 text-lg text-gray-900 dark:text-white">0</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Payments</label>
                                        <p class="mt-1 text-lg text-gray-900 dark:text-white">0</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($customer->notes)
                        <div class="mb-8">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>