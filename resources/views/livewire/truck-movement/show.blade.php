<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Truck Movement Details</flux:heading>
                <flux:subheading>View movement information</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button variant="primary" :href="route('truck-movements.edit', $truckMovement)" wire:navigate>
                    <flux:icon name="pencil" />
                    Edit Movement
                </flux:button>
                <flux:button variant="outline" :href="route('truck-movements.index')" wire:navigate>
                    <flux:icon name="arrow-left" />
                    Back to Movements
                </flux:button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Movement Information -->
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Movement Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Fare Amount</dt>
                            <dd class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">₵{{ number_format($truckMovement->fare, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Gas Chop Money</dt>
                            <dd class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">₵{{ number_format($truckMovement->gas_chop_money, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Net Balance</dt>
                            <dd class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">₵{{ number_format($truckMovement->balance, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</dt>
                            <dd class="mt-1">
                                <x-status-badge :status="$truckMovement->status_string" />
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">ATC Collection Date</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $truckMovement->atc_collection_date->format('F d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Load Dispatch Date</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $truckMovement->load_dispatch_date->format('F d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Created</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $truckMovement->created_at->format('M d, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $truckMovement->updated_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Related Information -->
        <div>
            <!-- Driver Information -->
            <div class="rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Driver Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <flux:avatar :name="$truckMovement->driver->name" size="lg" />
                        <div>
                            <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $truckMovement->driver->name }}</h4>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $truckMovement->driver->phone }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $truckMovement->driver->company }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <flux:button variant="outline" size="sm" :href="route('drivers.show', $truckMovement->driver)" wire:navigate>
                            <flux:icon name="eye" />
                            View Driver
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Truck Information -->
            <div class="mt-6 rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Truck Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Registration:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $truckMovement->truck->registration_number }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Cab Number:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $truckMovement->truck->cab_number }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Model:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $truckMovement->truck->truck_model }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Year:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $truckMovement->truck->year_of_manufacture }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <flux:button variant="outline" size="sm" :href="route('trucks.show', $truckMovement->truck)" wire:navigate>
                            <flux:icon name="eye" />
                            View Truck
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="mt-6 rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Customer Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <flux:avatar :name="$truckMovement->customer->name" size="lg" />
                        <div>
                            <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $truckMovement->customer->name }}</h4>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $truckMovement->customer->email }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $truckMovement->customer->phone }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <flux:button variant="outline" size="sm" :href="route('customers.show', $truckMovement->customer)" wire:navigate>
                            <flux:icon name="eye" />
                            View Customer
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>