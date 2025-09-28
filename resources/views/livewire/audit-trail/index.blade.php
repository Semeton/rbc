<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Audit Trails</flux:heading>
                <flux:subheading>Track all system activities and user actions</flux:subheading>
            </div>
            <div class="flex items-center space-x-3">
                <flux:button variant="outline" wire:click="clearFilters">
                    <flux:icon name="x-mark" class="w-4 h-4 mr-2" />
                    Clear Filters
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-zinc-900 rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label>Search</flux:label>
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search descriptions, actions, modules, or users..."
                    />
                </flux:field>
            </div>

            <!-- Module Filter -->
            <div>
                <flux:field>
                    <flux:label>Module</flux:label>
                    <select wire:model.live="module" class="select2-module w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Modules</option>
                        @foreach($this->modules as $moduleOption)
                            <option value="{{ $moduleOption }}">{{ ucfirst($moduleOption) }}</option>
                        @endforeach
                    </select>
                </flux:field>
            </div>

            <!-- Action Filter -->
            <div>
                <flux:field>
                    <flux:label>Action</flux:label>
                    <select wire:model.live="action" class="select2-action w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Actions</option>
                        @foreach($this->actions as $actionOption)
                            <option value="{{ $actionOption }}">{{ ucfirst($actionOption) }}</option>
                        @endforeach
                    </select>
                </flux:field>
            </div>

            <!-- User Filter -->
            <div>
                <flux:field>
                    <flux:label>User</flux:label>
                    <select wire:model.live="user" class="select2-user w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Users</option>
                        @foreach($this->users as $userOption)
                            <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
                        @endforeach
                    </select>
                </flux:field>
            </div>

            <!-- Date From -->
            <div>
                <flux:field>
                    <flux:label>Date From</flux:label>
                    <flux:input
                        type="date"
                        wire:model.live="dateFrom"
                    />
                </flux:field>
            </div>

            <!-- Date To -->
            <div>
                <flux:field>
                    <flux:label>Date To</flux:label>
                    <flux:input
                        type="date"
                        wire:model.live="dateTo"
                    />
                </flux:field>
            </div>
        </div>
    </div>

    <!-- Audit Trails Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Timestamp
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Action
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Module
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            IP Address
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->auditTrails as $auditTrail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $auditTrail->created_at->format('M j, Y') }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $auditTrail->created_at->format('g:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ substr($auditTrail->user_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $auditTrail->user_name }}
                                        </div>
                                        @if($auditTrail->user)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $auditTrail->user->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($auditTrail->action === 'created') bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400
                                    @elseif($auditTrail->action === 'updated') bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400
                                    @elseif($auditTrail->action === 'deleted') bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400
                                    @elseif($auditTrail->action === 'login') bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-400
                                    @elseif($auditTrail->action === 'logout') bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-400
                                    @else bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400
                                    @endif">
                                    {{ ucfirst($auditTrail->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <span class="font-medium">{{ ucfirst($auditTrail->module) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                <div class="max-w-xs truncate" title="{{ $auditTrail->description }}">
                                    {{ $auditTrail->description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $auditTrail->ip_address ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <flux:icon name="document-text" class="h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No audit trails found</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($this->auditTrails->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->auditTrails->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.select2-module').select2({
                placeholder: 'All Modules',
                allowClear: true,
                width: '100%'
            });

            $('.select2-action').select2({
                placeholder: 'All Actions',
                allowClear: true,
                width: '100%'
            });

            $('.select2-user').select2({
                placeholder: 'All Users',
                allowClear: true,
                width: '100%'
            });

            // Handle Livewire updates
            document.addEventListener('livewire:init', function() {
                Livewire.on('$refresh', () => {
                    $('.select2-module, .select2-action, .select2-user').select2('destroy');
                    
                    $('.select2-module').select2({
                        placeholder: 'All Modules',
                        allowClear: true,
                        width: '100%'
                    });

                    $('.select2-action').select2({
                        placeholder: 'All Actions',
                        allowClear: true,
                        width: '100%'
                    });

                    $('.select2-user').select2({
                        placeholder: 'All Users',
                        allowClear: true,
                        width: '100%'
                    });
                });
            });
        });
    </script>
</div>
