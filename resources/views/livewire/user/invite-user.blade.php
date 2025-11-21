<div>
    <!-- Header with Toggle -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">User Invitations</h2>
        </div>
    </div>

    <!-- Invitation Statistics -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="bg-white dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <flux:icon name="envelope" class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->invitationStats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <flux:icon name="clock" class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->invitationStats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <flux:icon name="check-circle" class="h-8 w-8 text-green-600 dark:text-green-400" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Accepted</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->invitationStats['accepted'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <flux:icon name="x-circle" class="h-8 w-8 text-red-600 dark:text-red-400" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expired</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->invitationStats['expired'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Send New Invitation Form -->
    <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg border border-gray-200 dark:border-gray-700 mb-5">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Send New Invitation</h3>
        
        <form wire:submit="invite">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input
                        wire:model="email"
                        type="email"
                        placeholder="user@example.com"
                        required
                    />
                    @error('email')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Role</flux:label>
                    <flux:select wire:model="role" required>
                        <option value="">Select a role</option>
                        @foreach($this->availableRoles as $roleKey => $roleName)
                            <option value="{{ $roleKey }}">{{ $roleName }}</option>
                        @endforeach
                    </flux:select>
                    @error('role')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                @if($this->availableRoles)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                        <div class="flex">
                            <flux:icon name="information-circle" class="h-5 w-5 text-blue-400" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Available Roles</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($this->availableRoles as $roleKey => $roleName)
                                            <li><strong>{{ $roleName }}</strong> - {{ $this->getRoleDescription($roleKey) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <flux:button variant="outline" wire:click="cancel" type="button">
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Send Invitation</span>
                    <span wire:loading>Sending...</span>
                </flux:button>
            </div>
        </form>
    </div>
    <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Latest Users</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Joined</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->recentUsers as $user)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                                <flux:badge variant="outline">{{ \Illuminate\Support\Str::headline($user->role) }}</flux:badge>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sent Invitations List -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sent Invitations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sent Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->sentInvitations as $invitation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $invitation->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400">
                                    {{ $invitation->role_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($invitation->isAccepted())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">
                                        Accepted
                                    </span>
                                @elseif($invitation->isExpired())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $invitation->created_at->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $invitation->expires_at->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($invitation->isPending())
                                        <flux:button 
                                            variant="outline" 
                                            size="sm" 
                                            wire:click="resendInvitation({{ $invitation->id }})"
                                            wire:loading.attr="disabled"
                                        >
                                            <span wire:loading.remove wire:target="resendInvitation({{ $invitation->id }})">Resend</span>
                                            <span wire:loading wire:target="resendInvitation({{ $invitation->id }})">Sending...</span>
                                        </flux:button>
                                    @endif
                                    
                                    @if(!$invitation->isAccepted())
                                        <flux:button 
                                            variant="outline" 
                                            size="sm" 
                                            wire:click="cancelInvitation({{ $invitation->id }})"
                                            wire:loading.attr="disabled"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                        >
                                            <span wire:loading.remove wire:target="cancelInvitation({{ $invitation->id }})">Cancel</span>
                                            <span wire:loading wire:target="cancelInvitation({{ $invitation->id }})">Cancelling...</span>
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <flux:icon name="envelope" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">No invitations sent yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>