<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Create Maintenance Record</flux:heading>
                <flux:subheading>Record a new truck maintenance</flux:subheading>
            </div>
            <flux:button variant="outline" :href="route('maintenance.index')" wire:navigate>
                <flux:icon name="arrow-left" />
                Back to Maintenance
            </flux:button>
        </div>
    </div>

    <div class="max-w-3xl">
        <form wire:submit="store" class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="space-y-6">
                    <div>
                        <flux:field>
                            <flux:label>Truck</flux:label>
                            <flux:select wire:model="truck_id" searchable>
                                <flux:select.option value="">Select a truck...</flux:select.option>
                                @foreach($this->trucks as $truck)
                                    <flux:select.option value="{{ $truck->id }}">{{ $truck->registration_number }} ({{ $truck->cab_number }})</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="truck_id" />
                        </flux:field>
                    </div>

                    <div>
                        <flux:field>
                            <flux:label>Description</flux:label>
                            <flux:textarea
                                wire:model="description"
                                placeholder="Describe the maintenance work performed..."
                                rows="4"
                            />
                            <flux:error name="description" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <flux:field>
                                <flux:label>Cost of Maintenance</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="cost_of_maintenance"
                                    placeholder="Enter maintenance cost..."
                                />
                                <flux:error name="cost_of_maintenance" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Date</flux:label>
                                <flux:input
                                    type="date"
                                    wire:model="maintenance_date"
                                />
                                <flux:error name="maintenance_date" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:select wire:model="status">
                                    <flux:select.option value="active">Active</flux:select.option>
                                    <flux:select.option value="inactive">Inactive</flux:select.option>
                                </flux:select>
                                <flux:error name="status" />
                            </flux:field>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <flux:button variant="outline" :href="route('maintenance.index')" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Create Maintenance</span>
                    <span wire:loading>Creating...</span>
                </flux:button>
            </div>
        </form>
    </div>

    <div class="mt-10">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Latest Maintenance Records</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Truck</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Description</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Cost (₦)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->recentMaintenanceRecords as $record)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ optional($record->truck)->registration_number ?? '—' }}
                                    @if(optional($record->truck)->cab_number)
                                        ({{ $record->truck->cab_number }})
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ \Illuminate\Support\Str::limit($record->description, 60) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ \Illuminate\Support\Carbon::parse($record->maintenance_date)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">
                                    ₦{{ number_format($record->cost_of_maintenance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No maintenance records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>