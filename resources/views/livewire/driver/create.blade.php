<div>
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Driver</h2>
                        <p class="text-gray-600 dark:text-gray-400">Add a new driver to your database</p>
                    </div>

                    <form wire:submit="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>Driver Name *</flux:label>
                                <flux:input
                                    wire:model="name"
                                    placeholder="Enter driver name"
                                />
                                @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </flux:field>

                            <flux:field>
                                <flux:label>Phone Number *</flux:label>
                                <flux:input
                                    wire:model="phone"
                                    placeholder="Enter phone number"
                                />
                                @error('phone') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>Company *</flux:label>
                            <flux:input
                                wire:model="company"
                                placeholder="Enter company name"
                            />
                            @error('company') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </flux:field>

                        <flux:field>
                            <flux:label>Driver Photo</flux:label>
                            <flux:input
                                type="file"
                                wire:model="photo"
                                accept="image/*"
                            />
                            @error('photo') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            @if($photo)
                                <div class="mt-2">
                                    <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-20 w-20 rounded-full object-cover">
                                </div>
                            @endif
                        </flux:field>

                        <flux:field>
                            <flux:label>Status *</flux:label>
                            <flux:select wire:model="status">
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="inactive">Inactive</flux:select.option>
                            </flux:select>
                            @error('status') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </flux:field>

                        <div class="flex justify-end space-x-3">
                            <flux:button variant="outline" href="{{ route('drivers.index') }}">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove>Create Driver</span>
                                <span wire:loading>Creating...</span>
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>