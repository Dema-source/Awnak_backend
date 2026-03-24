<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\RolePermissionRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionRepository implements RolePermissionRepositoryInterface
{
    /**
     * Assign permission to role.
     * 
     * @param Permission $permission
     * @param Role $role
     * @return bool
     */
    public function assignPermissionToRole(Permission $permission, Role $role)
    {
        return (bool) $role->givePermissionTo($permission);
    }

    /**
     * Remove permission from role.
     * 
     * @param Permission $permission
     * @param Role $role
     * @return bool
     */
    public function removePermissionFromRole(Permission $permission, Role $role)
    {
        return (bool) $role->revokePermissionTo($permission);
    }

    /**
     * Assign role to user.
     * 
     * @param Role $role
     * @param User $user
     * @return bool
     */
    public function assignRoleToUser(Role $role, User $user)
    {
        return (bool) $user->assignRole($role);
    }

    /**
     * Remove role from user.
     * 
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function revokeRoleFromUser(User $user, Role $role)
    {
        return (bool) $user->removeRole($role);
    }

    /**
     * Assign permission to user.
     * 
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function assignPermissionToUser(User $user, Permission $permission)
    {
        return (bool) $user->givePermissionTo($permission);
    }

    /**
     * Remove permission from user.
     * 
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function revokePermissionFromUser(User $user, Permission $permission)
    {
        return (bool)  $user->revokePermissionTo($permission);
    }

    /**
     * Check user permissions.
     * 
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function checkPermission(User $user, Permission $permission)
    {
        return (bool)  $user->hasPermissionTo($permission);
    }

    /**
     * Get all permissions for user.
     *  
     * @param User $user
     * @return collection
     */
    public function getUserPermissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }
}
