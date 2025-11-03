<div>
    <x-breadcrumb :items="[
        ['name' => 'ATCs', 'url' => route('atcs.index')],
        ['name' => 'Create ATC', 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-6">
                    Create New ATC
                </h3>

                <form wire:submit="store" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>Company</flux:label>
                            <select wire:model="company" class="select2-company w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select company...</option>
                                <option value="Dangote">Dangote</option>
                                <option value="BUA">BUA</option>
                                <option value="Mangal">Mangal</option>
                            </select>
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
                            <select wire:model="status" class="select2-status w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
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
                        <flux:button variant="outline" :href="route('atcs.index')" wire:navigate>
                            Cancel
                        </flux:button>
                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Create ATC</span>
                            <span wire:loading>Creating...</span>
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>