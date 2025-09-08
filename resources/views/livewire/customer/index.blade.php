<div>
    <!-- Customer Index Content -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Customers</h2>
                            <p class="text-gray-600 dark:text-gray-400">Manage your customer database</p>
                        </div>
                        <flux:button variant="primary" href="{{ route('customers.create') }}">
                            <flux:icon name="plus" class="w-4 h-4 mr-2" />
                            Add Customer
                        </flux:button>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-5">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Customers</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->statistics['total'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-5">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Customers</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->statistics['active'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-5">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Inactive Customers</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->statistics['inactive'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-5">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">New This Month</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->statistics['recent'] }}</div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <flux:field>
                                    <flux:label>Search</flux:label>
                                    <flux:input
                                        wire:model.live.debounce.300ms="search"
                                        placeholder="Search customers..."
                                    />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Status</flux:label>
                                    <flux:select wire:model.live="status">
                                        <flux:select.option value="">All Statuses</flux:select.option>
                                        <flux:select.option value="active">Active</flux:select.option>
                                        <flux:select.option value="inactive">Inactive</flux:select.option>
                                    </flux:select>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Per Page</flux:label>
                                    <flux:select wire:model.live="perPage">
                                        <flux:select.option value="10">10 per page</flux:select.option>
                                        <flux:select.option value="15">15 per page</flux:select.option>
                                        <flux:select.option value="25">25 per page</flux:select.option>
                                        <flux:select.option value="50">50 per page</flux:select.option>
                                        <flux:select.option value="100">100 per page</flux:select.option>
                                    </flux:select>
                                </flux:field>
                            </div>
                            <div class="flex items-end">
                                <flux:button variant="outline" wire:click="clearFilters">
                                    Clear Filters
                                </flux:button>
                            </div>
                        </div>
                    </div>

                    <!-- Customers Table -->
                    <div class="overflow-x-auto">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                                            Name
                                            @if($sortBy === 'name')
                                                <flux:icon :name="$sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'" class="w-4 h-4 inline ml-1" />
                                            @endif
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                                            Status
                                            @if($sortBy === 'status')
                                                <flux:icon :name="$sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'" class="w-4 h-4 inline ml-1" />
                                            @endif
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                            Created
                                            @if($sortBy === 'created_at')
                                                <flux:icon :name="$sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'" class="w-4 h-4 inline ml-1" />
                                            @endif
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 text-center uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($this->customers as $customer)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ $customer->initials }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $customer->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $customer->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $customer->phone }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$customer->status_string" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $customer->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <flux:button variant="outline" size="sm" href="{{ route('customers.show', ['customer' => $customer->id]) }}">
                                                        View
                                                    </flux:button>
                                                    <flux:button variant="outline" size="sm" href="{{ route('customers.edit', ['customer' => $customer->id]) }}">
                                                        Edit
                                                    </flux:button>
                                                    <flux:button 
                                                        variant="outline" 
                                                        size="sm" 
                                                        wire:click="deleteCustomer({{ (string) $customer->id }})"
                                                        wire:confirm="Are you sure you want to delete this customer?"
                                                    >
                                                        Delete
                                                    </flux:button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="text-gray-500 dark:text-gray-400">
                                                    <flux:icon name="users" class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                                                    <p class="text-lg font-medium">No customers found</p>
                                                    <p class="text-sm">Get started by adding your first customer.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $this->customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>