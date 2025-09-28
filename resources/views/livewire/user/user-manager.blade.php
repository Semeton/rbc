<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">User Management</flux:heading>
                <flux:subheading>Manage system users, roles, and permissions</flux:subheading>
            </div>
    </div>

    <!-- User Statistics -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white dark:bg-zinc-900 p-6 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="users" class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->userStats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white dark:bg-zinc-900 p-6 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="check-circle" class="h-8 w-8 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Users</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->userStats['active'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white dark:bg-zinc-900 p-6 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="clock" class="h-8 w-8 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Inactive Users</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->userStats['inactive'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white dark:bg-zinc-900 p-6 shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <flux:icon name="exclamation-triangle" class="h-8 w-8 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Suspended Users</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->userStats['suspended'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6">
        <div class="rounded-lg bg-white dark:bg-zinc-900 p-6 shadow border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <flux:field>
                        <flux:label>Search</flux:label>
                        <flux:input
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by name or email..."
                        />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Role</flux:label>
                        <flux:select wire:model.live="role">
                            <option value="">All Roles</option>
                            @foreach($this->availableRoles as $roleKey => $roleName)
                                <option value="{{ $roleKey }}">{{ $roleName }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model.live="status">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </flux:select>
                    </flux:field>
                </div>

                <div class="flex items-end">
                    <flux:button variant="outline" wire:click="resetFilters">
                        <flux:icon name="x-mark" />
                        Clear Filters
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="rounded-lg bg-white dark:bg-zinc-900 shadow border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Users</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $user->initials() }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400">
                                    {{ $user->role() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status_background_color_class }} {{ $user->status_color_class }}">
                                    {{ $user->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($user->status === 'active')
                                        <flux:button variant="outline" size="sm" wire:click="updateUserStatus({{ $user->id }}, 'inactive')">
                                            Deactivate
                                        </flux:button>
                                    @elseif($user->status === 'inactive')
                                        <flux:button variant="outline" size="sm" wire:click="updateUserStatus({{ $user->id }}, 'active')">
                                            Activate
                                        </flux:button>
                                    @endif

                                    @if($user->status === 'suspended')
                                        <flux:button variant="outline" size="sm" wire:click="updateUserStatus({{ $user->id }}, 'active')">
                                            Unsuspend
                                        </flux:button>
                                    @else
                                        <flux:button variant="outline" size="sm" wire:click="updateUserStatus({{ $user->id }}, 'suspended')">
                                            Suspend
                                        </flux:button>
                                    @endif

                                    <flux:button variant="outline" size="sm" wire:click="deleteUser({{ $user->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                        Delete
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <flux:icon name="users" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($this->users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->users->links() }}
            </div>
        @endif
    </div>

    <!-- Invite User Modal -->
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-zinc-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                                    Invite New User
                                </h3>
                                
                                <livewire:user.invite-user />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>