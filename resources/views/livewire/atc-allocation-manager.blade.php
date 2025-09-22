<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">ATC Allocation Manager</h1>
                <p class="text-gray-600">Monitor and manage ATC tonnage allocation across customers</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <flux:icon name="truck" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total ATCs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $allocationStats['total_atcs'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <flux:icon name="check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Available</p>
                        <p class="text-2xl font-bold text-green-600">{{ $allocationStats['available'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <flux:icon name="exclamation-triangle" class="w-6 h-6 text-yellow-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Fully Allocated</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $allocationStats['fully_allocated'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <flux:icon name="x-circle" class="w-6 h-6 text-red-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Over Allocated</p>
                        <p class="text-2xl font-bold text-red-600">{{ $allocationStats['over_allocated'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tons Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tonnage Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($allocationStats['total_tons'], 2) }}</p>
                    <p class="text-sm text-gray-600">Total Tons</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($allocationStats['allocated_tons'], 2) }}</p>
                    <p class="text-sm text-gray-600">Allocated Tons</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($allocationStats['remaining_tons'], 2) }}</p>
                    <p class="text-sm text-gray-600">Remaining Tons</p>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search by ATC number or company..." 
                        class="w-full"
                    />
                </div>
                <div class="md:w-48">
                    <flux:select wire:model.live="filter" class="w-full">
                        <option value="all">All ATCs</option>
                        <option value="available">Available</option>
                        <option value="fully_allocated">Fully Allocated</option>
                        <option value="over_allocated">Over Allocated</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- ATCs Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ATC Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Allocation Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tons
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transactions
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($atcs as $atc)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            ATC #{{ $atc->atc_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $atc->company }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ ucfirst(str_replace('_', ' ', $atc->atc_type)) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $allocation = $atc->allocation_summary;
                                    @endphp
                                    <div class="flex items-center">
                                        @if($allocation['is_over_allocated'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Over Allocated
                                            </span>
                                        @elseif($allocation['is_fully_allocated'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Fully Allocated
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Available
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ min(100, $allocation['allocation_percentage']) }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $allocation['allocation_percentage'] }}% allocated
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">{{ number_format($allocation['allocated_tons'], 2) }} / {{ number_format($allocation['total_tons'], 2) }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ number_format($allocation['remaining_tons'], 2) }} remaining
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $allocation['transactions_count'] }} transaction(s)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <flux:button 
                                        wire:click="showAtcDetails({{ $atc->id }})" 
                                        variant="outline" 
                                        size="sm"
                                    >
                                        View Details
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No ATCs found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $atcs->links() }}
            </div>
        </div>
    </div>
</div>