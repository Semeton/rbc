<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create New Transaction</h1>
                <p class="text-gray-600 dark:text-gray-400">Add a new daily customer transaction with ATC allocation</p>
            </div>
            <flux:button 
                wire:click="resetForm" 
                variant="outline"
            >
                <flux:icon name="arrow-path" class="w-4 h-4 mr-2" />
                Reset Form
            </flux:button>
        </div>

        <!-- Transaction Form -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Basic Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:field>
                                <flux:label>Customer *</flux:label>
                                <select wire:model="customer_id" required class="select2-customer w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Customer</option>
                                    @foreach($this->customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Driver *</flux:label>
                                <select wire:model="driver_id" required class="select2-driver w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Driver</option>
                                    @foreach($this->drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Date *</flux:label>
                                <flux:input type="date" wire:model="date" required />
                                @error('date') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Status *</flux:label>
                                <select wire:model="status" required class="select2-status w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>
                    </div>
                </div>

                <!-- ATC Allocation -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">ATC Allocation</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <flux:field>
                                <flux:label>ATC *</flux:label>
                                <select wire:model="atc_id" required class="select2-atc w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select ATC</option>
                                    @foreach($this->atcs as $atc)
                                        <option value="{{ $atc->id }}">
                                            ATC #{{ $atc->atc_number }} ({{ $atc->company }}) - 
                                            {{ number_format($atc->remaining_tons, 2) }} tons left
                                        </option>
                                    @endforeach
                                </select>
                                @error('atc_id') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                            
                            @if($atc_id)
                                @php
                                    $selectedAtc = $this->atcs->firstWhere('id', $atc_id);
                                @endphp
                                @if($selectedAtc)
                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <div class="text-sm text-blue-800 dark:text-blue-200">
                                            <div class="font-medium text-lg mb-2">ATC #{{ $selectedAtc->atc_number }} Capacity Details</div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <div class="font-medium">Total Capacity</div>
                                                    <div>{{ number_format($selectedAtc->tons, 2) }} tons (₦{{ number_format($selectedAtc->amount, 2) }})</div>
                                                </div>
                                                <div>
                                                    <div class="font-medium">Price per Ton</div>
                                                    <div>₦{{ number_format($selectedAtc->price_per_ton, 2) }}</div>
                                                </div>
                                                <div>
                                                    <div class="font-medium">Already Allocated</div>
                                                    <div>{{ number_format($selectedAtc->allocated_tons, 2) }} tons (₦{{ number_format($selectedAtc->transactions()->where('status', true)->sum('atc_cost'), 2) }})</div>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-green-700 dark:text-green-400">Available</div>
                                                    <div class="text-green-700 dark:text-green-400">{{ number_format($selectedAtc->remaining_tons, 2) }} tons (₦{{ number_format($selectedAtc->remaining_amount, 2) }})</div>
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-700">
                                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                                    Allocation: {{ number_format($selectedAtc->allocation_percentage, 1) }}% used
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Tons *</flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="tons" required />
                                @error('tons') <flux:error>{{ $message }}</flux:error> @enderror
                                
                                @if($atc_id && $tons)
                                    @php
                                        $selectedAtc = $this->atcs->firstWhere('id', $atc_id);
                                    @endphp
                                    @if($selectedAtc)
                                        @php
                                            $remainingTons = $selectedAtc->remaining_tons;
                                            $isOverAllocated = (float) $tons > $remainingTons;
                                        @endphp
                                        <div class="mt-2 p-2 rounded text-sm {{ $isOverAllocated ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800' }}">
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
                                <flux:label>Cost (₦) *</flux:label>
                                <flux:input type="number" step="0.01" wire:model="atc_cost" required />
                                @error('atc_cost') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Transport Cost</flux:label>
                                <flux:input type="number" step="0.01" wire:model="transport_cost" />
                                @error('transport_cost') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Transaction Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:field>
                                <flux:label>Origin *</flux:label>
                                <flux:input wire:model="origin" required />
                                @error('origin') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Destination *</flux:label>
                                <flux:input wire:model="destination" required />
                                @error('destination') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Cement Type *</flux:label>
                                <flux:input wire:model="cement_type" required />
                                @error('cement_type') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>

                        

                        <div class="md:col-span-2">
                            <flux:field>
                                <flux:label>Depot Details</flux:label>
                                <flux:textarea wire:model="deport_details" rows="3" />
                                @error('deport_details') <flux:error>{{ $message }}</flux:error> @enderror
                            </flux:field>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6">
                    <flux:button 
                        type="button" 
                        wire:click="resetForm" 
                        variant="outline"
                    >
                        Reset Form
                    </flux:button>
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="save">Create Transaction</span>
                        <span wire:loading wire:target="save">Creating...</span>
                    </flux:button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Latest Transactions</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Driver</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ATC</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Tons</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Cost (₦)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->recentTransactions as $transaction)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $transaction->date?->format('M d, Y') ?? $transaction->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $transaction->customer->name ?? '—' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $transaction->driver->name ?? '—' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                    @if($transaction->atc)
                                        #{{ $transaction->atc->atc_number }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">
                                    {{ number_format($transaction->tons, 2) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">
                                    ₦{{ number_format($transaction->atc_cost, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No transactions recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>