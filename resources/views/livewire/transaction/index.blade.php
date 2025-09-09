<div>
    <x-breadcrumb :items="[
        ['name' => 'Daily Transactions', 'url' => route('transactions.index')]
    ]" />

    <!-- Statistics Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="clipboard-document-list" class="h-6 w-6 text-gray-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Transactions</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $this->statistics['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="check-circle" class="h-6 w-6 text-green-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Transactions</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $this->statistics['active'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="x-circle" class="h-6 w-6 text-red-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Inactive Transactions</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $this->statistics['inactive'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="clock" class="h-6 w-6 text-blue-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Recent (30 days)</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $this->statistics['recent'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Statistics -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="currency-dollar" class="h-6 w-6 text-green-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total ATC Cost</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">${{ number_format($this->statistics['total_atc_cost'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="truck" class="h-6 w-6 text-blue-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Transport Cost</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">${{ number_format($this->statistics['total_transport_cost'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="calendar-days" class="h-6 w-6 text-purple-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Today's Transactions</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $this->statistics['today_transactions'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="chart-bar" class="h-6 w-6 text-yellow-400" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">This Month</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $this->statistics['month_transactions'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions and Filters -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <flux:button variant="primary" :href="route('transactions.create')" wire:navigate>
                <flux:icon name="plus" class="h-4 w-4" />
                Create Transaction
            </flux:button>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <flux:field>
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by origin, destination..."
                />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="">All Status</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Customer</flux:label>
                <flux:select wire:model.live="customer_id">
                    <flux:select.option value="0">All Customers</flux:select.option>
                    @foreach($this->customers as $customer)
                        <flux:select.option value="{{ $customer->id }}">{{ $customer->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Per Page</flux:label>
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="10">10</flux:select.option>
                    <flux:select.option value="15">15</flux:select.option>
                    <flux:select.option value="25">25</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                    <flux:select.option value="100">100</flux:select.option>
                </flux:select>
            </flux:field>
        </div>
    </div>

    <!-- Additional Filters -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
        <flux:field>
            <flux:label>Driver</flux:label>
            <flux:select wire:model.live="driver_id">
                <flux:select.option value="0">All Drivers</flux:select.option>
                @foreach($this->drivers as $driver)
                    <flux:select.option value="{{ $driver->id }}">{{ $driver->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>ATC</flux:label>
            <flux:select wire:model.live="atc_id">
                <flux:select.option value="0">All ATCs</flux:select.option>
                @foreach($this->atcs as $atc)
                    <flux:select.option value="{{ $atc->id }}">ATC #{{ $atc->atc_number }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Date From</flux:label>
            <flux:input
                wire:model.live="date_from"
                type="date"
            />
        </flux:field>

        <flux:field>
            <flux:label>Date To</flux:label>
            <flux:input
                wire:model.live="date_to"
                type="date"
            />
        </flux:field>
    </div>

    <!-- Clear Filters -->
    @if($search || $status || $customer_id || $driver_id || $atc_id || $date_from || $date_to || $cement_type)
        <div class="mb-4">
            <flux:button variant="outline" wire:click="clearFilters">
                <flux:icon name="x-mark" class="h-4 w-4" />
                Clear Filters
            </flux:button>
        </div>
    @endif

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-zinc-800 shadow overflow-hidden sm:rounded-md">
        @if($this->transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Driver
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Route
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cement Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total Cost
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->customer->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->driver->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->origin }} → {{ $transaction->destination }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->cement_type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    ${{ number_format($transaction->total_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$transaction->status_string" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <flux:button variant="outline" size="sm" :href="route('transactions.show', $transaction)" wire:navigate>
                                            <flux:icon name="eye" class="h-4 w-4" />
                                        </flux:button>
                                        <flux:button variant="outline" size="sm" :href="route('transactions.edit', $transaction)" wire:navigate>
                                            <flux:icon name="pencil" class="h-4 w-4" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->transactions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <flux:icon name="clipboard-document-list" class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No transactions found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new transaction.</p>
                <div class="mt-6">
                    <flux:button variant="primary" :href="route('transactions.create')" wire:navigate>
                        <flux:icon name="plus" class="h-4 w-4" />
                        Create Transaction
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</div>
