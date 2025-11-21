<div>
    <!-- Customer Create Form -->
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Customer</h2>
                        <p class="text-gray-600 dark:text-gray-400">Add a new customer to your database</p>
                    </div>

                    <form wire:submit="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Customer Name *</flux:label>
                                    <flux:input wire:model="name" placeholder="Enter customer name" />
                                    <flux:error name="name" />
                                </flux:field>
                            </div>

                            <!-- Email (optional) -->
                            <div>
                                <flux:field>
                                    <flux:label>Email Address</flux:label>
                                    <flux:input wire:model="email" type="email" placeholder="customer@example.com" />
                                    <flux:error name="email" />
                                </flux:field>
                            </div>

                            <!-- Phone -->
                            <div>
                                <flux:field>
                                    <flux:label>Phone Number *</flux:label>
                                    <flux:input wire:model="phone" placeholder="+1 (555) 123-4567" />
                                    <flux:error name="phone" />
                                </flux:field>
                            </div>

                            <!-- Status (optional) -->
                            <div>
                                <flux:field>
                                    <flux:label>Status</flux:label>
                                    <select wire:model="status" class="select2-status w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <flux:error name="status" />
                                </flux:field>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Notes</flux:label>
                                    <flux:textarea wire:model="notes" placeholder="Additional notes about the customer" rows="3" />
                                    <flux:error name="notes" />
                                </flux:field>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <flux:button variant="outline" href="{{ route('customers.index') }}">
                                Cancel
                            </flux:button>
                            <flux:button 
                                variant="primary" 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            >
                                <span wire:loading.remove wire:target="save">Create Customer</span>
                                <span wire:loading wire:target="save">
                                    <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                                    Creating...
                                </span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Latest Customers</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Phone</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->recentCustomers as $customer)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $customer->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $customer->phone }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $customer->email ?: '—' }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <x-status-badge :status="$customer->status" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No customers recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>