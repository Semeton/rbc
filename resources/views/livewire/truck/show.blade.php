<div>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $truck->display_name }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Truck Details</p>
                        </div>
                        <div class="flex space-x-3">
                            <flux:button variant="outline" href="{{ route('trucks.edit', $truck) }}">
                                Edit Truck
                            </flux:button>
                            <flux:button 
                                variant="outline" 
                                wire:click="deleteTruck"
                                wire:confirm="Are you sure you want to delete this truck?"
                            >
                                Delete Truck
                            </flux:button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Truck Information -->
                        <div class="lg:col-span-2">
                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Truck Information</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center mb-6">
                                        <div class="w-20 h-20 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center mr-4">
                                            <flux:icon name="truck" class="w-10 h-10 text-gray-600 dark:text-gray-400" />
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $truck->display_name }}</h4>
                                            <p class="text-gray-600 dark:text-gray-400">{{ $truck->cab_number }}</p>
                                            <div class="mt-2">
                                                <x-status-badge :status="$truck->status_string" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Number</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $truck->registration_number }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cab Number</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $truck->cab_number }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Truck Model</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $truck->truck_model }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year of Manufacture</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $truck->year_of_manufacture }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Age</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $truck->age }} years</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                            <div class="mt-1">
                                                <x-status-badge :status="$truck->status_string" />
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $truck->created_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $truck->updated_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="space-y-6">
                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Maintenance Records</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $truck->maintenanceRecords->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Truck Records</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $truck->truckRecords->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Maintenance Cost</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($truck->total_maintenance_cost, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                                <div class="space-y-3">
                                    <flux:button variant="primary" href="{{ route('trucks.edit', $truck) }}" class="w-full">
                                        Edit Truck
                                    </flux:button>
                                    <flux:button variant="outline" href="{{ route('trucks.index') }}" class="w-full">
                                        Back to Trucks
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>