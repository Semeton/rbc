<div>
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Truck</h2>
                        <p class="text-gray-600 dark:text-gray-400">Add a new truck to your fleet</p>
                    </div>

                    <form wire:submit="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                            <flux:label>Cab Number</flux:label>
                                <flux:input
                                    wire:model="cab_number"
                                    placeholder="Enter cab number"
                                />
                                @error('cab_number') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </flux:field>

                            <flux:field>
                                <flux:label>Registration Number *</flux:label>
                                <flux:input
                                    wire:model="registration_number"
                                    placeholder="Enter registration number"
                                />
                                @error('registration_number') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>Truck Model</flux:label>
                            <flux:input
                                wire:model="truck_model"
                                placeholder="Enter truck model"
                            />
                            @error('truck_model') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </flux:field>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>Year of Manufacture</flux:label>
                                <flux:input
                                    type="number"
                                    wire:model="year_of_manufacture"
                                    placeholder="Enter year"
                                    min="1900"
                                    max="{{ now()->year }}"
                                />
                                @error('year_of_manufacture') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </flux:field>

                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:select wire:model="status">
                                    <flux:select.option value="active">Active</flux:select.option>
                                    <flux:select.option value="inactive">Inactive</flux:select.option>
                                </flux:select>
                                @error('status') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </flux:field>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <flux:button variant="outline" href="{{ route('trucks.index') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove>Create Truck</span>
                                <span wire:loading>Creating...</span>
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Latest Trucks</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cab No.</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Registration</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Model</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Year</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->recentTrucks as $truck)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $truck->cab_number ?: '—' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $truck->registration_number }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $truck->truck_model ?: '—' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $truck->year_of_manufacture ?: '—' }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <x-status-badge :status="$truck->status" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No trucks recorded yet.
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