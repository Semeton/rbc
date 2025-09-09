<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Truck Maintenance</flux:heading>
                <flux:subheading>Track truck maintenance records and costs</flux:subheading>
            </div>
            <flux:button variant="primary" :href="route('maintenance.create')" wire:navigate>
                <flux:icon name="plus" />
                Add Maintenance
            </flux:button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="wrench-screwdriver" class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Total Records</dt>
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
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Total Cost</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">₵{{ number_format($this->statistics['total_cost'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="chart-bar" class="h-6 w-6 text-orange-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Average Cost</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">₵{{ number_format($this->statistics['average_cost'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="clock" class="h-6 w-6 text-purple-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Recent (30 days)</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ $this->statistics['recent'] }}</dd>
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
                        placeholder="Search by description or truck..."
                    />
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
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="">All Status</flux:select.option>
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
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
                    <flux:label>Min Cost</flux:label>
                    <flux:input
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model.live="cost_min"
                        placeholder="Minimum cost..."
                    />
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>Max Cost</flux:label>
                    <flux:input
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model.live="cost_max"
                        placeholder="Maximum cost..."
                    />
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

    <!-- Maintenance Records Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Truck</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->maintenanceRecords as $maintenance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $maintenance->truck->registration_number }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $maintenance->truck->cab_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100 max-w-xs truncate">{{ $maintenance->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                ₵{{ number_format($maintenance->cost_of_maintenance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$maintenance->status_string" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $maintenance->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <flux:button variant="ghost" size="sm" :href="route('maintenance.show', $maintenance)" wire:navigate>
                                        <flux:icon name="eye" />
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" :href="route('maintenance.edit', $maintenance)" wire:navigate>
                                        <flux:icon name="pencil" />
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="wrench-screwdriver" class="mx-auto h-12 w-12 text-zinc-400" />
                                <div class="mt-2">No maintenance records found</div>
                                <div class="mt-1">Get started by creating a new maintenance record</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->maintenanceRecords->links() }}
    </div>
</div>