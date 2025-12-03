<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Edit Truck Movement</flux:heading>
                <flux:subheading>Update truck movement details</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button variant="outline" :href="route('truck-movements.show', $truckMovement)" wire:navigate>
                    <flux:icon name="eye" />
                    View Movement
                </flux:button>
                <flux:button variant="outline" :href="route('truck-movements.index')" wire:navigate>
                    <flux:icon name="arrow-left" />
                    Back to Movements
                </flux:button>
            </div>
        </div>
    </div>

    <div class="max-w-3xl">
        <form wire:submit="update" class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-zinc-800">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <flux:field>
                                <flux:label>Driver</flux:label>
                                <flux:select wire:model="driver_id" searchable>
                                    <flux:select.option value="">Select a driver...</flux:select.option>
                                    @foreach($this->drivers as $driver)
                                        <flux:select.option value="{{ $driver->id }}">{{ $driver->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="driver_id" />
                            </flux:field>
                        </div>

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
                                <flux:label>Customer</flux:label>
                                <flux:select wire:model="customer_id" searchable>
                                    <flux:select.option value="">Select a customer...</flux:select.option>
                                    @foreach($this->customers as $customer)
                                        <flux:select.option value="{{ $customer->id }}">{{ $customer->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="customer_id" />
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
                                <flux:label>Fare (Customer Cost - ATC Cost)</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="fare"
                                    readonly
                                />
                                <flux:error name="fare" />
                            </flux:field>
                        </div>

                        <div>
                            <flux:field>
                                <flux:label>ATC Cost</flux:label>
                                <flux:input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="atc_cost"
                                    readonly
                                />
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
                                    :value="$fare - $gas_chop_money + ($haulage ?? 0)"
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
                                    :value="($fare - $gas_chop_money + ($haulage ?? 0)) + ($incentive ?? 0)"
                                    readonly
                                />
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
                <flux:button variant="outline" :href="route('truck-movements.show', $truckMovement)" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Movement</span>
                    <span wire:loading>Updating...</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>