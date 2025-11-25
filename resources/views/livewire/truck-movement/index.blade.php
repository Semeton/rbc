<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Truck Movements</flux:heading>
                <flux:subheading>Track daily truck movements and costs</flux:subheading>
            </div>
            <flux:button variant="primary" :href="route('truck-movements.create')" wire:navigate>
                <flux:icon name="plus" />
                Add Movement
            </flux:button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="truck" class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Total Movements</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ $this->statistics['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="currency-dollar" class="h-6 w-6 text-green-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Total Fare</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->statistics['total_fare'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="currency-dollar" class="h-6 w-6 text-orange-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Total Gas Chop</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->statistics['total_gas_chop'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="chart-bar" class="h-6 w-6 text-purple-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Net Balance</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->statistics['total_balance'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters with Searchable Selects -->
    <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <flux:field>
                    <flux:label>Search</flux:label>
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by driver, truck, or customer..."
                    />
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>Driver</flux:label>
                    <flux:select wire:model.live="driver_id" searchable>
                        <flux:select.option value="">All Drivers</flux:select.option>
                        @foreach($this->drivers as $driver)
                            <flux:select.option value="{{ $driver->id }}">{{ $driver->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>Truck</flux:label>
                    <flux:select wire:model.live="truck_id" searchable>
                        <flux:select.option value="">All Trucks</flux:select.option>
                        @foreach($this->trucks as $truck)
                            <flux:select.option value="{{ $truck->id }}">{{ $truck->registration_number }} ({{ $truck->cab_number }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div>
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

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <flux:field>
                    <flux:label>Customer</flux:label>
                    <flux:select wire:model.live="customer_id" searchable>
                        <flux:select.option value="">All Customers</flux:select.option>
                        @foreach($this->customers as $customer)
                            <flux:select.option value="{{ $customer->id }}">{{ $customer->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="">All Status</flux:select.option>
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex items-end">
                <flux:button variant="outline" wire:click="clearFilters">
                    <flux:icon name="x-mark" />
                    Clear Filters
                </flux:button>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <flux:field>
                    <flux:label>Date From</flux:label>
                    <flux:input
                        type="date"
                        wire:model.live="date_from"
                    />
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>Date To</flux:label>
                    <flux:input
                        type="date"
                        wire:model.live="date_to"
                    />
                </flux:field>
            </div>
        </div>
    </div>

    <!-- Truck Movements Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Driver</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Truck</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Collection Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Dispatch Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Fare</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Gas Chop</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Balance</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->truckMovements as $movement)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <flux:avatar :name="$movement->driver->name" />
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $movement->driver->name }}</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $movement->driver->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $movement->truck?->registration_number ?? 'N/A' }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $movement->truck->cab_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $movement->customer?->name ?? 'N/A' }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $movement->customer?->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $movement->atc_collection_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $movement->load_dispatch_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                ₦{{ number_format($movement->fare, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                ₦{{ number_format($movement->gas_chop_money, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                ₦{{ number_format($movement->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <flux:button variant="ghost" size="sm" :href="route('truck-movements.show', $movement)" wire:navigate>
                                        <flux:icon name="eye" />
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" :href="route('truck-movements.edit', $movement)" wire:navigate>
                                        <flux:icon name="pencil" />
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="truck" class="mx-auto h-12 w-12 text-zinc-400" />
                                <div class="mt-2">No truck movements found</div>
                                <div class="mt-1">Get started by creating a new truck movement</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->truckMovements->links() }}
    </div>
</div>
