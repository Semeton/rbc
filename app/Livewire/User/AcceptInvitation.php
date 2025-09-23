<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\UserInvitation;
use App\User\Services\InvitationService;
use App\User\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AcceptInvitation extends Component
{
    public string $token = '';
    public string $name = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
    ];

    protected $messages = [
        'name.required' => 'Name is required.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];

    public function mount(string $token): void
    {
        $this->token = $token;
        
        $invitationService = app(InvitationService::class);
        $invitation = $invitationService->validateInvitationToken($token);
        
        if (!$invitation) {
            abort(404, 'Invalid or expired invitation.');
        }
    }

    public function updatedPassword(): void
    {
        $this->validateOnly('password');
    }

    public function updatedPasswordConfirmation(): void
    {
        $this->validateOnly('password_confirmation');
    }

    public function accept(): void
    {
        $this->validate();

        $invitationService = app(InvitationService::class);
        $userService = app(UserService::class);

        // Validate invitation again
        $invitation = $invitationService->validateInvitationToken($this->token);
        
        if (!$invitation) {
            $this->addError('token', 'Invalid or expired invitation.');
            return;
        }

        try {
            // Create user from invitation
            $user = $userService->acceptInvitation($this->token, [
                'name' => $this->name,
                'password' => $this->password,
            ]);

            // Log in the user
            Auth::login($user);

            // Redirect to dashboard
            $this->redirect(route('dashboard'));
            
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to create account. Please try again.');
        }
    }

    public function getInvitationProperty(): ?UserInvitation
    {
        $invitationService = app(InvitationService::class);
        return $invitationService->getInvitationByToken($this->token);
    }

    public function render(): View
    {
        return view('livewire.user.accept-invitation');
    }
}
