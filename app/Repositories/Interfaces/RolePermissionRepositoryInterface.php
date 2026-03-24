<?php

namespace App\Repositories\Interfaces;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Interface RolePermissionRepositoryInterface
 *
 * Define operations related to Roles and Permissions
 */
interface RolePermissionRepositoryInterface
{
    /**
     * Assign permission to role.
     * 
     * @param Permission $permission
     * @param Role $role
     * @return bool
     */
    public function assignPermissionToRole(Permission $permission, Role $role);
    
    /**
     * Remove permission from role.
     * 
     * @param Permission $permission
     * @param Role $role
     * @return bool
     */
    public function removePermissionFromRole(Permission $permission, Role $role);

    /**
     * Assign role to user.
     * 
     * @param Role $role
     * @param User $user
     * @return bool
     */
    public function assignRoleToUser(Role $role, User $user);

    /**
     * Remove role from user.
     * 
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function revokeRoleFromUser(User $user, Role $role);

    /**
     * Assign permission to user.
     * 
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function assignPermissionToUser(User $user, Permission $permission);

    /**
     * Remove permission from user.
     * 
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function revokePermissionFromUser(User $user, Permission $permission);

    /**
     * Check user permissions.
     * 
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function checkPermission(User $user, Permission $permission);

    /**
     * Get all permissions for user.
     * 
     * @param User $user
     * @return Collection
     */
    public function getUserPermissions(User $user);
}
