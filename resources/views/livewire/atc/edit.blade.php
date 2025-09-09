<div>
    <x-breadcrumb :items="[
        ['name' => 'ATCs', 'url' => route('atcs.index')],
        ['name' => 'Edit ATC', 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-6">
                    Edit ATC #{{ $atc->atc_number }}
                </h3>

                <form wire:submit="update" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>Company</flux:label>
                            <flux:input
                                wire:model="company"
                                placeholder="Enter company name"
                            />
                            <flux:error name="company" />
                        </flux:field>

                        <flux:field>
                            <flux:label>ATC Number</flux:label>
                            <flux:input
                                wire:model="atc_number"
                                type="number"
                                placeholder="Enter ATC number"
                            />
                            <flux:error name="atc_number" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>ATC Type</flux:label>
                            <flux:select wire:model="atc_type">
                                <flux:select.option value="bg">BG</flux:select.option>
                                <flux:select.option value="cash_payment">Cash Payment</flux:select.option>
                            </flux:select>
                            <flux:error name="atc_type" />
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

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>Amount</flux:label>
                            <flux:input
                                wire:model="amount"
                                type="number"
                                step="0.01"
                                placeholder="Enter amount"
                            />
                            <flux:error name="amount" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Tons</flux:label>
                            <flux:input
                                wire:model="tons"
                                type="number"
                                placeholder="Enter tons"
                            />
                            <flux:error name="tons" />
                        </flux:field>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <flux:button variant="outline" :href="route('atcs.show', $atc)" wire:navigate>
                            Cancel
                        </flux:button>
                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Update ATC</span>
                            <span wire:loading>Updating...</span>
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>