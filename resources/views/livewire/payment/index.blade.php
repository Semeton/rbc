<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Payments</flux:heading>
                <flux:subheading>Manage customer payments and track balances</flux:subheading>
            </div>
            <flux:button variant="primary" :href="route('payments.create')" wire:navigate>
                <flux:icon name="plus" />
                Add Payment
            </flux:button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="banknotes" class="h-6 w-6 text-green-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Total Payments</dt>
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
                        <flux:icon name="currency-dollar" class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Total Amount</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->statistics['total_amount'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="clock" class="h-6 w-6 text-orange-600" />
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

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="chart-bar" class="h-6 w-6 text-purple-600" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 truncate dark:text-zinc-400">Average Amount</dt>
                            <dd class="text-lg font-medium text-zinc-900 dark:text-zinc-100">₦{{ number_format($this->statistics['average_amount'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <flux:field>
                    <flux:label>Search</flux:label>
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by notes, bank, or customer..."
                    />
                </flux:field>
            </div>

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
                    <flux:label>Bank Name</flux:label>
                    <flux:input
                        wire:model.live.debounce.300ms="bank_name"
                        placeholder="Filter by bank..."
                    />
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
                    <flux:label>Amount Min</flux:label>
                    <flux:input
                        type="number"
                        step="0.01"
                        wire:model.live.debounce.300ms="amount_min"
                        placeholder="Minimum amount..."
                    />
                </flux:field>
            </div>

            <div>
                <flux:field>
                    <flux:label>Amount Max</flux:label>
                    <flux:input
                        type="number"
                        step="0.01"
                        wire:model.live.debounce.300ms="amount_max"
                        placeholder="Maximum amount..."
                    />
                </flux:field>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-zinc-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Payment Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Notes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-800 dark:divide-zinc-700">
                    @forelse($this->payments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <flux:avatar :name="$payment->customer->name" />
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $payment->customer->name }}</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $payment->customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $payment->payment_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                ₦{{ $payment->formatted_amount }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $payment->bank_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ Str::limit($payment->notes, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <flux:button variant="ghost" size="sm" :href="route('payments.show', $payment)" wire:navigate>
                                        <flux:icon name="eye" />
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" :href="route('payments.edit', $payment)" wire:navigate>
                                        <flux:icon name="pencil" />
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="banknotes" class="mx-auto h-12 w-12 text-zinc-400" />
                                <div class="mt-2">No payments found</div>
                                <div class="mt-1">Get started by creating a new payment</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->payments->links() }}
    </div>
</div>