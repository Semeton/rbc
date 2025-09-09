<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Maintenance Record Details</flux:heading>
                <flux:subheading>View maintenance information</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button variant="primary" :href="route('maintenance.edit', $maintenance)" wire:navigate>
                    <flux:icon name="pencil" />
                    Edit Record
                </flux:button>
                <flux:button variant="outline" :href="route('maintenance.index')" wire:navigate>
                    <flux:icon name="arrow-left" />
                    Back to Maintenance
                </flux:button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Maintenance Information -->
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Maintenance Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Cost of Maintenance</dt>
                            <dd class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">₦{{ number_format($maintenance->cost_of_maintenance, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</dt>
                            <dd class="mt-1">
                                <x-status-badge :status="$maintenance->status_string" />
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Description</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 whitespace-pre-wrap">{{ $maintenance->description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Created</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $maintenance->created_at->format('M d, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $maintenance->updated_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Truck Information -->
        <div>
            <div class="rounded-lg bg-white shadow dark:bg-zinc-800">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Truck Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Registration:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $maintenance->truck->registration_number }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Cab Number:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $maintenance->truck->cab_number }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Model:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $maintenance->truck->truck_model }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Year:</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400 ml-2">{{ $maintenance->truck->year_of_manufacture }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <flux:button variant="outline" size="sm" :href="route('trucks.show', $maintenance->truck)" wire:navigate>
                            <flux:icon name="eye" />
                            View Truck
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>