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
                                wire:model.live.debounce.500ms="atc_number"
                                type="number"
                                placeholder="Enter ATC number"
                            />
                            <flux:error name="atc_number" />

                            @if(! is_null($atcNumberExists))
                                <p class="mt-2 text-sm {{ $atcNumberExists ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ $atcNumberExists ? 'This ATC number already exists. Please use a unique number.' : 'ATC number is available.' }}
                                </p>
                            @endif
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

    <div class="mt-10">
        <div class="bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100">Latest ATCs</h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-900/60">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ATC #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tons</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->recentAtcs as $recentAtc)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        #{{ $recentAtc->atc_number }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $recentAtc->company }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                                        {{ number_format($recentAtc->tons) }} tons
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <x-status-badge :status="$recentAtc->status_string" />
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $recentAtc->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No ATCs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>