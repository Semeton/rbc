<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\User;
use App\User\Services\InvitationService;
use App\User\Services\RoleService;
use App\User\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InviteUser extends Component
{
    public string $email = '';
    public string $role = '';
    public bool $showInviteForm = true;

    protected $rules = [
        'email' => 'required|email|unique:users,email|unique:user_invitations,email',
        'role' => 'required|in:admin,accountant,operations_manager,staff',
    ];

    protected $messages = [
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email address is already in use.',
        'role.required' => 'Please select a role.',
        'role.in' => 'Please select a valid role.',
    ];

    public function mount(): void
    {
        // Initialize component
    }

    public function updatedEmail(): void
    {
        $this->validateOnly('email');
    }

    public function updatedRole(): void
    {
        $this->validateOnly('role');
    }

    public function invite(): void
    {
        $this->validate();

        $userService = app(UserService::class);
        $invitationService = app(InvitationService::class);

        try {
            // Create invitation
            $invitation = $userService->inviteUser(
                $this->email,
                $this->role,
                Auth::user()
            );

            // Send invitation email
            $emailSent = $invitationService->sendInvitation($invitation);

            if ($emailSent) {
                // Reset form
                $this->reset(['email', 'role']);

                // Dispatch event
                $this->dispatch('user-invited', [
                    'message' => 'Invitation sent successfully!',
                    'type' => 'success'
                ]);
            } else {
                $this->addError('email', 'Failed to send invitation email. Please try again.');
            }
        } catch (\Exception $e) {
            $this->addError('email', 'Failed to create invitation: ' . $e->getMessage());
        }
    }

    public function resendInvitation(int $invitationId): void
    {
        $invitationService = app(InvitationService::class);
        $invitation = \App\Models\UserInvitation::find($invitationId);

        if (!$invitation) {
            $this->addError('general', 'Invitation not found.');
            return;
        }

        try {
            $emailSent = $invitationService->resendInvitation($invitation);

            if ($emailSent) {
                $this->dispatch('invitation-resent', [
                    'message' => 'Invitation resent successfully!',
                    'type' => 'success'
                ]);
            } else {
                $this->addError('general', 'Failed to resend invitation email.');
            }
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to resend invitation: ' . $e->getMessage());
        }
    }

    public function cancelInvitation(int $invitationId): void
    {
        $invitationService = app(InvitationService::class);
        $invitation = \App\Models\UserInvitation::find($invitationId);

        if (!$invitation) {
            $this->addError('general', 'Invitation not found.');
            return;
        }

        try {
            $cancelled = $invitationService->cancelInvitation($invitation);

            if ($cancelled) {
                $this->dispatch('invitation-cancelled', [
                    'message' => 'Invitation cancelled successfully!',
                    'type' => 'success'
                ]);
            } else {
                $this->addError('general', 'Failed to cancel invitation.');
            }
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to cancel invitation: ' . $e->getMessage());
        }
    }

    public function toggleView(): void
    {
        $this->showInviteForm = !$this->showInviteForm;
    }

    public function cancel(): void
    {
        $this->reset(['email', 'role']);
        $this->dispatch('close-modal');
    }

    public function getAvailableRolesProperty(): array
    {
        $roleService = app(RoleService::class);
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }
        
        return $roleService->getManageableRoles($user);
    }

    public function getRoleDescription(string $role): string
    {
        return match ($role) {
            'admin' => 'Full system access and user management',
            'accountant' => 'Financial data access and reporting',
            'operations_manager' => 'Operations oversight and management',
            'staff' => 'Basic system access for daily operations',
            default => 'Standard user access',
        };
    }

    public function getSentInvitationsProperty()
    {
        $userService = app(UserService::class);
        return $userService->getPendingInvitations();
    }

    public function getInvitationStatsProperty(): array
    {
        $invitationService = app(InvitationService::class);
        return $invitationService->getInvitationStats();
    }

    public function render(): View
    {
        return view('livewire.user.invite-user');
    }
}
