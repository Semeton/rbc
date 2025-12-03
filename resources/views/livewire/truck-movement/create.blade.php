<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Create Truck Movement</flux:heading>
                <flux:subheading>Record a new daily truck movement</flux:subheading>
            </div>
            <flux:button variant="outline" :href="route('truck-movements.index')" wire:navigate>
                <flux:icon name="arrow-left" />
                Back to Movements
            </flux:button>
        </div>
    </div>

    <div class="max-w-3xl">
        <form wire:submit="store" class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <flux:field>
                                <flux:label>Driver</flux:label>
                                <select wire:model="driver_id" class="select2-driver w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select a driver...</option>
                                    @foreach($this->drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                <flux:error name="driver_id" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Truck</flux:label>
                                <select wire:model="truck_id" class="select2-truck w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select a truck...</option>
                                    @foreach($this->trucks as $truck)
                                        <option value="{{ $truck->id }}">{{ $truck->registration_number }} ({{ $truck->cab_number }})</option>
                                    @endforeach
                                </select>
                                <flux:error name="truck_id" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Customer</flux:label>
                                <select wire:model="customer_id" class="select2-customer w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select a customer...</option>
                                    @foreach($this->customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                <flux:error name="customer_id" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>ATC</flux:label>
                                <select wire:model.live="atc_id" class="select2-atc w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select an ATC...</option>
                                    @foreach($this->atcs as $atc)
                                        <option value="{{ $atc->id }}">ATC #{{ $atc->atc_number }} ({{ $atc->company }})</option>
                                    @endforeach
                                </select>
                                <flux:error name="atc_id" />
                            </flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <flux:field>
                                <flux:label>ATC Collection Date</flux:label>
                                <flux:input
                                    type="date"
                                    wire:model="atc_collection_date"
                                />
                                <flux:error name="atc_collection_date" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Load Dispatch Date</flux:label>
                                <flux:input
                                    type="date"
                                    wire:model="load_dispatch_date"
                                />
                                <flux:error name="load_dispatch_date" />
                            </flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                        <div>
                            <flux:field>
                                <flux:label>Customer Cost</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model.live="customer_cost"
                                    placeholder="Enter customer's cost"
                                />
                                <flux:error name="customer_cost" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>ATC Cost</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ $atc_cost }}"
                                    readonly
                                />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Fare (Customer Cost - ATC Cost)</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ $fare }}"
                                    readonly
                                />
                                <flux:error name="fare" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Gas Chop Money</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model.live="gas_chop_money"
                                    placeholder="Enter gas chop amount..."
                                />
                                <flux:error name="gas_chop_money" />
                            </flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <flux:field>
                                <flux:label>Haulage</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    wire:model.live="haulage"
                                    placeholder="Enter haulage (can be negative)"
                                />
                                <flux:error name="haulage" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Incentive</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    wire:model.live="incentive"
                                    placeholder="Enter incentive"
                                />
                                <flux:error name="incentive" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Salary Contribution</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model.live="salary_contribution"
                                    placeholder="Enter salary contribution"
                                />
                                <flux:error name="salary_contribution" />
                            </flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <flux:field>
                                <flux:label>Total (Fare - Gas + Haulage)</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    value="{{ $total_amount }}"
                                    readonly
                                />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Total + Incentive</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    value="{{ $total_plus_incentive }}"
                                    readonly
                                />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>Status</flux:label>
                                <select wire:model="status" class="select2-status w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <flux:error name="status" />
                            </flux:field>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <flux:button variant="outline" :href="route('truck-movements.index')" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Create Movement</span>
                    <span wire:loading>Creating...</span>
                </flux:button>
            </div>
        </form>
    </div>

    <div class="mt-10">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Latest Truck Movements</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Driver</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Truck</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Dispatch Date</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Fare (₦)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->recentTruckMovements as $movement)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $movement->driver->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ optional($movement->truck)->registration_number }} {{ optional($movement->truck)->cab_number ? '(' . $movement->truck->cab_number . ')' : '' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $movement->customer->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $movement->load_dispatch_date ? \Illuminate\Support\Carbon::parse($movement->load_dispatch_date)->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">
                                    ₦{{ number_format($movement->fare, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No truck movements recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>