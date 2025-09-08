<div>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $driver->name }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Driver Details</p>
                        </div>
                        <div class="flex space-x-3">
                            <flux:button variant="outline" href="{{ route('drivers.edit', $driver) }}">
                                Edit Driver
                            </flux:button>
                            <flux:button 
                                variant="outline" 
                                wire:click="deleteDriver"
                                wire:confirm="Are you sure you want to delete this driver?"
                            >
                                Delete Driver
                            </flux:button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Driver Information -->
                        <div class="lg:col-span-2">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Driver Information</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mr-4">
                                            @if($driver->photo_url)
                                                <img class="w-20 h-20 rounded-full object-cover" src="{{ $driver->photo_url }}" alt="{{ $driver->name }}">
                                            @else
                                                <span class="text-lg font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $driver->initials }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $driver->name }}</h4>
                                            <p class="text-gray-600 dark:text-gray-400">{{ $driver->company }}</p>
                                            <div class="mt-2">
                                                <x-status-badge :status="$driver->status_string" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $driver->phone }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $driver->company }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                            <div class="mt-1">
                                                <x-status-badge :status="$driver->status_string" />
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $driver->created_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="space-y-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Transactions</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $driver->transactions->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Truck Records</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $driver->truckRecords->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $driver->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                                <div class="space-y-3">
                                    <flux:button variant="primary" href="{{ route('drivers.edit', $driver) }}" class="w-full">
                                        Edit Driver
                                    </flux:button>
                                    <flux:button variant="outline" href="{{ route('drivers.index') }}" class="w-full">
                                        Back to Drivers
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