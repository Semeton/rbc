<div>
    <x-breadcrumb :items="[
        ['name' => 'Daily Transactions', 'url' => route('transactions.index')],
        ['name' => 'Create Transaction', 'url' => '#']
    ]" />

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-6">
                    Create New Transaction
                </h3>

                <form wire:submit="store" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>Customer</flux:label>
                            <flux:select wire:model="customer_id">
                                <flux:select.option value="0">Select Customer</flux:select.option>
                                @foreach($this->customers as $customer)
                                    <flux:select.option value="{{ $customer->id }}">{{ $customer->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="customer_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Driver</flux:label>
                            <flux:select wire:model="driver_id">
                                <flux:select.option value="0">Select Driver</flux:select.option>
                                @foreach($this->drivers as $driver)
                                    <flux:select.option value="{{ $driver->id }}">{{ $driver->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="driver_id" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>ATC</flux:label>
                            <flux:select wire:model="atc_id">
                                <flux:select.option value="0">Select ATC</flux:select.option>
                                @foreach($this->atcs as $atc)
                                    <flux:select.option value="{{ $atc->id }}">ATC #{{ $atc->atc_number }} - {{ $atc->company }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="atc_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Date</flux:label>
                            <flux:input
                                wire:model="date"
                                type="date"
                            />
                            <flux:error name="date" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>Origin</flux:label>
                            <flux:input
                                wire:model="origin"
                                placeholder="Enter origin location"
                            />
                            <flux:error name="origin" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Destination</flux:label>
                            <flux:input
                                wire:model="destination"
                                placeholder="Enter destination location"
                            />
                            <flux:error name="destination" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>Cement Type</flux:label>
                            <flux:input
                                wire:model="cement_type"
                                placeholder="Enter cement type"
                            />
                            <flux:error name="cement_type" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="status">
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="inactive">Inactive</flux:select.option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Deport Details</flux:label>
                        <flux:textarea
                            wire:model="deport_details"
                            placeholder="Enter deport details (optional)"
                            rows="3"
                        />
                        <flux:error name="deport_details" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>ATC Cost</flux:label>
                            <flux:input
                                wire:model="atc_cost"
                                type="number"
                                step="0.01"
                                placeholder="Enter ATC cost"
                            />
                            <flux:error name="atc_cost" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Transport Cost</flux:label>
                            <flux:input
                                wire:model="transport_cost"
                                type="number"
                                step="0.01"
                                placeholder="Enter transport cost"
                            />
                            <flux:error name="transport_cost" />
                        </flux:field>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <flux:button variant="outline" :href="route('transactions.index')" wire:navigate>
                            Cancel
                        </flux:button>
                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Create Transaction</span>
                            <span wire:loading>Creating...</span>
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>