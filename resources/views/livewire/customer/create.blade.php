<div>
    <!-- Customer Create Form -->
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Customer</h2>
                        <p class="text-gray-600 dark:text-gray-400">Add a new customer to your database</p>
                    </div>

                    <form wire:submit="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Customer Name *</flux:label>
                                    <flux:input wire:model="name" placeholder="Enter customer name" />
                                    <flux:error name="name" />
                                </flux:field>
                            </div>

                            <!-- Email -->
                            <div>
                                <flux:field>
                                    <flux:label>Email Address *</flux:label>
                                    <flux:input wire:model="email" type="email" placeholder="customer@example.com" />
                                    <flux:error name="email" />
                                </flux:field>
                            </div>

                            <!-- Phone -->
                            <div>
                                <flux:field>
                                    <flux:label>Phone Number *</flux:label>
                                    <flux:input wire:model="phone" placeholder="+1 (555) 123-4567" />
                                    <flux:error name="phone" />
                                </flux:field>
                            </div>

                            <!-- Status -->
                            <div>
                                <flux:field>
                                    <flux:label>Status *</flux:label>
                                    <flux:select wire:model="status">
                                        <flux:select.option value="active">Active</flux:select.option>
                                        <flux:select.option value="inactive">Inactive</flux:select.option>
                                    </flux:select>
                                    <flux:error name="status" />
                                </flux:field>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Notes</flux:label>
                                    <flux:textarea wire:model="notes" placeholder="Additional notes about the customer" rows="3" />
                                    <flux:error name="notes" />
                                </flux:field>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <flux:button variant="outline" href="{{ route('customers.index') }}">
                                Cancel
                            </flux:button>
                            <flux:button 
                                variant="primary" 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            >
                                <span wire:loading.remove wire:target="save">Create Customer</span>
                                <span wire:loading wire:target="save">
                                    <flux:icon name="arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                                    Creating...
                                </span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>