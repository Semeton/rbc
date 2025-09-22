<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Transaction Management</h1>
                <p class="text-gray-600">Manage daily customer transactions with ATC allocation</p>
            </div>
            <flux:button wire:click="showCreateForm" variant="primary">
                <flux:icon name="plus" class="w-4 h-4 mr-2" />
                Add Transaction
            </flux:button>
        </div>

        <!-- ATC Allocation Statistics -->
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
                        <p class="text-sm font-medium text-gray-600">Available ATCs</p>
                        <p class="text-2xl font-bold text-green-600">{{ $allocationStats['available_atcs'] }}</p>
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
                        <p class="text-2xl font-bold text-yellow-600">{{ $allocationStats['fully_allocated_atcs'] }}</p>
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
                        <p class="text-2xl font-bold text-red-600">{{ $allocationStats['over_allocated_atcs'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search transactions..." 
                        class="w-full"
                    />
                </div>
                <div class="md:w-48">
                    <flux:select wire:model.live="filter" class="w-full">
                        <option value="all">All Transactions</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transaction Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ATC & Allocation
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Remaining Capacity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Costs
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $transaction->customer->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $transaction->driver->name }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ $transaction->date->format('M d, Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            ATC #{{ $transaction->atc->atc_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ number_format($transaction->tons, 2) }} tons
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($transaction->atc->remaining_tons, 2) }} tons left
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ₦{{ number_format($transaction->atc->remaining_amount, 2) }} left
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ number_format($transaction->atc->allocation_percentage, 1) }}% allocated
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">₦{{ number_format($transaction->atc_cost, 2) }}</div>
                                        <div class="text-xs text-gray-500">
                                            + ₦{{ number_format($transaction->transport_cost, 2) }} transport
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $transaction->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <flux:button 
                                        wire:click="showEditForm({{ $transaction->id }})" 
                                        variant="outline" 
                                        size="sm"
                                    >
                                        Edit
                                    </flux:button>
                                    <flux:button 
                                        wire:click="delete({{ $transaction->id }})" 
                                        wire:confirm="Are you sure you want to delete this transaction?"
                                        variant="outline" 
                                        size="sm"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        Delete
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
</div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- Transaction Form Modal -->
    @if($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="resetForm(); showForm = false">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $isEditing ? 'Edit Transaction' : 'Create New Transaction' }}
                        </h3>
                        <flux:button wire:click="resetForm(); showForm = false" variant="outline" size="sm">
                            ×
                        </flux:button>
                    </div>
                    
                    <form wire:submit="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:field>
                                    <flux:label>Customer</flux:label>
                                    <flux:select wire:model="customer_id" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('customer_id') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Driver</flux:label>
                                    <flux:select wire:model="driver_id" required>
                                        <option value="">Select Driver</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('driver_id') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>ATC</flux:label>
                                    <flux:select wire:model="atc_id" required>
                                        <option value="">Select ATC</option>
                                        @foreach($atcs as $atc)
                                            <option value="{{ $atc->id }}">
                                                ATC #{{ $atc->atc_number }} ({{ $atc->company }}) - 
                                                {{ number_format($atc->remaining_tons, 2) }} tons left
                                            </option>
                                        @endforeach
                                    </flux:select>
                                    @error('atc_id') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                                
                                @if($atc_id)
                                    @php
                                        $selectedAtc = $atcs->firstWhere('id', $atc_id);
                                    @endphp
                                    @if($selectedAtc)
                                        <div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <div class="text-sm text-blue-800">
                                                <div class="font-medium">ATC #{{ $selectedAtc->atc_number }} Capacity</div>
                                                <div class="mt-1 space-y-1">
                                                    <div>Total: {{ number_format($selectedAtc->tons, 2) }} tons (₦{{ number_format($selectedAtc->amount, 2) }})</div>
                                                    <div>Price per ton: ₦{{ number_format($selectedAtc->price_per_ton, 2) }}</div>
                                                    <div>Allocated: {{ number_format($selectedAtc->allocated_tons, 2) }} tons (₦{{ number_format($selectedAtc->transactions()->where('status', true)->sum('atc_cost'), 2) }})</div>
                                                    <div class="font-medium text-green-700">
                                                        Remaining: {{ number_format($selectedAtc->remaining_tons, 2) }} tons (₦{{ number_format($selectedAtc->remaining_amount, 2) }})
                                                    </div>
                                                    <div class="text-xs text-gray-600">
                                                        {{ number_format($selectedAtc->allocation_percentage, 1) }}% allocated
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Date</flux:label>
                                    <flux:input type="date" wire:model="date" required />
                                    @error('date') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Origin</flux:label>
                                    <flux:input wire:model="origin" required />
                                    @error('origin') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Destination</flux:label>
                                    <flux:input wire:model="destination" required />
                                    @error('destination') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Cement Type</flux:label>
                                    <flux:input wire:model="cement_type" required />
                                    @error('cement_type') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Tons</flux:label>
                                    <flux:input type="number" step="0.01" wire:model.live="tons" required />
                                    @error('tons') <flux:error>{{ $message }}</flux:error> @enderror
                                    
                                    @if($atc_id && $tons)
                                        @php
                                            $selectedAtc = $atcs->firstWhere('id', $atc_id);
                                        @endphp
                                        @if($selectedAtc)
                                            @php
                                                $remainingTons = $selectedAtc->remaining_tons;
                                                $isOverAllocated = (float) $tons > $remainingTons;
                                            @endphp
                                            <div class="mt-2 text-sm {{ $isOverAllocated ? 'text-red-600' : 'text-green-600' }}">
                                                @if($isOverAllocated)
                                                    ⚠️ Exceeds remaining capacity by {{ number_format((float) $tons - $remainingTons, 2) }} tons
                                                @else
                                                    ✅ {{ number_format($remainingTons - (float) $tons, 2) }} tons remaining after allocation
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>ATC Cost (₦)</flux:label>
                                    <flux:input type="number" step="0.01" wire:model="atc_cost" required />
                                    @error('atc_cost') <flux:error>{{ $message }}</flux:error> @enderror
                                    
                                    @if($atc_id && $tons)
                                        @php
                                            $selectedAtc = $atcs->firstWhere('id', $atc_id);
                                        @endphp
                                        @if($selectedAtc)
                                            @php
                                                $expectedCost = (float) $tons * $selectedAtc->price_per_ton;
                                                $isCorrect = abs((float) $atc_cost - $expectedCost) <= 0.01;
                                            @endphp
                                            <div class="mt-2 text-sm {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                                @if($isCorrect)
                                                    ✅ Correctly calculated: {{ number_format($tons, 2) }} tons × ₦{{ number_format($selectedAtc->price_per_ton, 2) }} = ₦{{ number_format($expectedCost, 2) }}
                                                @else
                                                    ⚠️ Should be: {{ number_format($tons, 2) }} tons × ₦{{ number_format($selectedAtc->price_per_ton, 2) }} = ₦{{ number_format($expectedCost, 2) }}
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Transport Cost (₦)</flux:label>
                                    <flux:input type="number" step="0.01" wire:model="transport_cost" required />
                                    @error('transport_cost') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Status</flux:label>
                                    <flux:select wire:model="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </flux:select>
                                    @error('status') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <div>
                                <flux:field>
                                    <flux:label>Depot Details</flux:label>
                                    <flux:textarea wire:model="deport_details" />
                                    @error('deport_details') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <flux:button type="button" wire:click="resetForm(); showForm = false" variant="outline">
                                Cancel
                            </flux:button>
                            <flux:button type="submit" variant="primary">
                                {{ $isEditing ? 'Update Transaction' : 'Create Transaction' }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>