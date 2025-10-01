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
</div>