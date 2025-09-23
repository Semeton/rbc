<div>
    <!-- Header with Toggle -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">User Invitations</h2>
        </div>
    </div>

    <!-- Invitation Statistics -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <flux:icon name="envelope" class="h-8 w-8 text-blue-600" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->invitationStats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <flux:icon name="clock" class="h-8 w-8 text-yellow-600" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->invitationStats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <flux:icon name="check-circle" class="h-8 w-8 text-green-600" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Accepted</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->invitationStats['accepted'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                    <flux:icon name="x-circle" class="h-8 w-8 text-red-600" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Expired</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->invitationStats['expired'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Send New Invitation Form -->
    <div class="bg-white p-6 rounded-lg border border-gray-200 mb-5">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Send New Invitation</h3>
        
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
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <flux:icon name="information-circle" class="h-5 w-5 text-blue-400" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Available Roles</h3>
                                <div class="mt-2 text-sm text-blue-700">
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
    <!-- Sent Invitations List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Sent Invitations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->sentInvitations as $invitation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invitation->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $invitation->role_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($invitation->isAccepted())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Accepted
                                    </span>
                                @elseif($invitation->isExpired())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $invitation->created_at->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
                                            class="text-red-600 hover:text-red-800"
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
                                <flux:icon name="envelope" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                <p class="text-sm text-gray-500">No invitations sent yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>