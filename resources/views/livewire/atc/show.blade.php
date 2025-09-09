<div>
    <x-breadcrumb :items="[
        ['name' => 'ATCs', 'url' => route('atcs.index')],
        ['name' => 'ATC #' . $atc->atc_number, 'url' => '#']
    ]" />

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-zinc-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                            ATC #{{ $atc->atc_number }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Created {{ $atc->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="outline" :href="route('atcs.edit', $atc)" wire:navigate>
                            <flux:icon name="pencil" class="h-4 w-4" />
                            Edit
                        </flux:button>
                        <flux:button variant="outline" :href="route('atcs.index')" wire:navigate>
                            <flux:icon name="arrow-left" class="h-4 w-4" />
                            Back to ATCs
                        </flux:button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Company</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $atc->company }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ATC Type</dt>
                        <dd class="mt-1">
                            <flux:badge variant="outline">{{ $atc->atc_type }}</flux:badge>
                        </dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
                            <x-status-badge :status="$atc->status_string" />
                        </dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">${{ number_format($atc->amount, 2) }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tons</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($atc->tons) }}</dd>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $atc->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                    </div>
                </div>

                @if($atc->transactions()->count() > 0)
                    <div class="mt-8">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Related Transactions</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                This ATC has {{ $atc->transactions()->count() }} related transaction(s).
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>