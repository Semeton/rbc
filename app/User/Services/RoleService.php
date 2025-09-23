<?php

declare(strict_types=1);

namespace App\User\Services;

use App\Models\User;
use App\Traits\UserRolesAndPermission;

class RoleService
{
    use UserRolesAndPermission;

    /**
     * Get all available roles
     */
    public function getAllRoles(): array
    {
        return [
            'admin' => $this->roles('admin'),
            'accountant' => $this->roles('accountant'),
            'operations_manager' => $this->roles('operations_manager'),
            'staff' => $this->roles('staff'),
        ];
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions(string $role): array
    {
        return $this->permissions($role);
    }

    /**
     * Check if user has permission
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        $userPermissions = $this->permissions($user->role);
        return in_array($permission, $userPermissions);
    }

    /**
     * Check if user has any of the permissions
     */
    public function userHasAnyPermission(User $user, array $permissions): bool
    {
        $userPermissions = $this->permissions($user->role);
        return !empty(array_intersect($permissions, $userPermissions));
    }

    /**
     * Check if user has all permissions
     */
    public function userHasAllPermissions(User $user, array $permissions): bool
    {
        $userPermissions = $this->permissions($user->role);
        return empty(array_diff($permissions, $userPermissions));
    }

    /**
     * Get role hierarchy
     */
    public function getRoleHierarchy(): array
    {
        return [
            'admin' => 4,
            'accountant' => 3,
            'operations_manager' => 2,
            'staff' => 1,
        ];
    }

    /**
     * Check if role is higher than another role
     */
    public function isRoleHigher(string $role1, string $role2): bool
    {
        $hierarchy = $this->getRoleHierarchy();
        return ($hierarchy[$role1] ?? 0) > ($hierarchy[$role2] ?? 0);
    }

    /**
     * Check if user can manage another user
     */
    public function canManageUser(User $manager, User $target): bool
    {
        // Users cannot manage themselves
        if ($manager->id === $target->id) {
            return false;
        }

        // Check role hierarchy
        return $this->isRoleHigher($manager->role, $target->role);
    }

    /**
     * Get manageable roles for a user
     */
    public function getManageableRoles(User $user): array
    {
        $allRoles = $this->getAllRoles();
        $userLevel = $this->getRoleHierarchy()[$user->role] ?? 0;

        if ($user->role === 'admin') {
            return $allRoles;
        }

        return array_filter($allRoles, function ($role) use ($userLevel) {
            $roleLevel = $this->getRoleHierarchy()[$role] ?? 0;
            return $roleLevel < $userLevel;
        }, ARRAY_FILTER_USE_KEY);
    }
}
