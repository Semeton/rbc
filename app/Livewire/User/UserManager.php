<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Models\User;
use App\User\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';
    public string $status = '';
    public bool $showInviteModal = false;

    public function mount(): void
    {
        // Initialize component
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->role = '';
        $this->status = '';
        $this->resetPage();
    }

    public function showInviteModal(): void
    {
        $this->showInviteModal = true;
    }

    public function hideInviteModal(): void
    {
        $this->showInviteModal = false;
    }

    public function updateUserStatus(User $user, string $status): void
    {
        $userService = app(UserService::class);
        $userService->updateUserStatus($user, $status);
        
        $this->dispatch('user-updated');
    }

    public function deleteUser(User $user): void
    {
        $userService = app(UserService::class);
        $userService->deleteUser($user);
        
        $this->dispatch('user-deleted');
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $userService = app(UserService::class);
        
        $filters = [];
        if ($this->search) {
            $filters['search'] = $this->search;
        }
        if ($this->role) {
            $filters['role'] = $this->role;
        }
        if ($this->status) {
            $filters['status'] = $this->status;
        }

        return $userService->getPaginatedUsers($filters);
    }

    #[Computed]
    public function userStats(): array
    {
        $userService = app(UserService::class);
        return $userService->getUserStats();
    }

    #[Computed]
    public function availableRoles(): array
    {
        $roleService = app(\App\User\Services\RoleService::class);
        return $roleService->getAllRoles();
    }

    public function render(): View
    {
        return view('livewire.user.user-manager');
    }
}
