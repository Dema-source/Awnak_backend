<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    /**
     * Assign permission to user.
     * @param User $user
     * @param Permission $permission
     * @return void
     */
    public function assign_permission_to_user(User $user, Permission $permission)
    {
        $user->givePermissionTo($permission);
    }

    /**
     * Revoke permission to user.
     * @param User $user
     * @param Permission $permission
     * @return void
     */
    public function revoke_permission_to_user(User $user, Permission $permission)
    {
        $user->revokePermissionTo($permission);
    }

    /**
     * Assign permission to role.
     * @param Permission $permission
     * @param Role $role
     * @return void
     */
    public function assign_permission_to_role(Permission $permission, Role $role)
    {
        $role->givePermissionTo($permission);
    }

    /**
     * Assign role to user.
     * @param Role $role
     * @param User $user
     * @return void
     */
    public function assign_role_to_user(Role $role, User $user)
    {
        $user->assignRole($role);
    }

    /**
     * Check the user permissions.
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    public function get_user_permissions(User $user)
    {
        $permissions = $user->getAllPermissions();
        return $permissions;
    }
}
