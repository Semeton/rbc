<?php

declare(strict_types=1);

namespace App\User\Services;

use App\Models\User;
use App\Models\UserInvitation;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        private readonly AuditTrailService $auditTrailService
    ) {}

    /**
     * Get paginated users with filters
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with(['invitations', 'sessions'])
            ->latest('created_at');

        // Apply filters
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        $user = User::create($data);

        $this->auditTrailService->log(
            'create',
            'User',
            "User '{$user->name}' was created"
        );

        return $user;
    }

    /**
     * Update an existing user
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        $this->auditTrailService->log(
            'update',
            'User',
            "User '{$user->name}' was updated"
        );

        return $user;
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user): bool
    {
        $name = $user->name;
        $result = $user->delete();

        if ($result) {
            $this->auditTrailService->log(
                'delete',
                'User',
                "User '{$name}' was deleted"
            );
        }

        return $result;
    }

    /**
     * Invite a new user
     */
    public function inviteUser(string $email, string $role, User $invitedBy): UserInvitation
    {
        $token = Str::random(64);
        $expiresAt = now()->addDays(7);

        $invitation = UserInvitation::create([
            'email' => $email,
            'role' => $role,
            'token' => $token,
            'invited_by' => $invitedBy->id,
            'expires_at' => $expiresAt,
        ]);

        $this->auditTrailService->log(
            'create',
            'UserInvitation',
            "User invitation sent to '{$email}' for role '{$role}'"
        );

        return $invitation;
    }

    /**
     * Accept an invitation and create user
     */
    public function acceptInvitation(string $token, array $userData): User
    {
        $invitation = UserInvitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->firstOrFail();

        $user = User::create([
            'name' => $userData['name'],
            'email' => $invitation->email,
            'role' => $invitation->role,
            'password' => $userData['password'],
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        $this->auditTrailService->log(
            'create',
            'User',
            "User '{$user->name}' was created from invitation"
        );

        return $user;
    }

    /**
     * Update user status
     */
    public function updateUserStatus(User $user, string $status): bool
    {
        $oldStatus = $user->status;
        $result = $user->update(['status' => $status]);

        if ($result) {
            $this->auditTrailService->log(
                'update',
                'User',
                "User '{$user->name}' status changed from '{$oldStatus}' to '{$status}'"
            );
        }

        return $result;
    }

    /**
     * Get user activity
     */
    public function getUserActivity(User $user, int $limit = 10): Collection
    {
        return $this->auditTrailService->getUserActivity($user->id, $limit);
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'by_role' => User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->where('last_login_at', '>', now()->subDays(7))
                ->count(),
        ];
    }

    /**
     * Get pending invitations
     */
    public function getPendingInvitations(): Collection
    {
        return UserInvitation::with('invitedBy')
            ->pending()
            ->latest()
            ->get();
    }

    /**
     * Get expired invitations
     */
    public function getExpiredInvitations(): Collection
    {
        return UserInvitation::with('invitedBy')
            ->expired()
            ->latest()
            ->get();
    }

    /**
     * Clean up expired invitations
     */
    public function cleanupExpiredInvitations(): int
    {
        $count = UserInvitation::expired()->count();
        
        if ($count > 0) {
            UserInvitation::expired()->delete();
            
            $this->auditTrailService->log(
                'delete',
                'UserInvitation',
                "{$count} expired invitations were cleaned up"
            );
        }

        return $count;
    }
}
