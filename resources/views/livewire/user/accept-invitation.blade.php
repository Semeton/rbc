<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <flux:icon name="user-plus" class="h-6 w-6 text-blue-600" />
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Accept Invitation
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Complete your account setup to join RBC Trucking Management System
            </p>
        </div>

        @if($this->invitation)
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <flux:icon name="information-circle" class="h-5 w-5 text-blue-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Invitation Details</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p><strong>Email:</strong> {{ $this->invitation->email }}</p>
                            <p><strong>Role:</strong> {{ $this->invitation->role_display }}</p>
                            <p><strong>Expires:</strong> {{ $this->invitation->expires_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit="accept" class="mt-8 space-y-6">
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Full Name</flux:label>
                        <flux:input
                            wire:model="name"
                            type="text"
                            placeholder="Enter your full name"
                            required
                        />
                        @error('name')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>Password</flux:label>
                        <flux:input
                            wire:model="password"
                            type="password"
                            placeholder="Enter a secure password"
                            required
                        />
                        @error('password')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>Confirm Password</flux:label>
                        <flux:input
                            wire:model="password_confirmation"
                            type="password"
                            placeholder="Confirm your password"
                            required
                        />
                        @error('password_confirmation')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                @error('general')
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <flux:icon name="exclamation-triangle" class="h-5 w-5 text-red-400" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    {{ $message }}
                                </div>
                            </div>
                        </div>
                    </div>
                @enderror

                <div>
                    <flux:button variant="primary" type="submit" class="w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove>Create Account</span>
                        <span wire:loading>Creating Account...</span>
                    </flux:button>
                </div>
            </form>
        @else
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <flux:icon name="exclamation-triangle" class="h-5 w-5 text-red-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Invalid Invitation</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>This invitation is invalid, expired, or has already been used.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>