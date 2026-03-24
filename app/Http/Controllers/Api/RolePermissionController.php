<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * RolePermissionController Constructor.
     * 
     * @param RolePermissionService $rolepermissionService
     */
    public function __construct(protected RolePermissionService $rolepermissionService) {}

    /**
     * Assign permission to role.
     * 
     * @param Permission $permission
     * @param Role $role
     * @return JsonResponse
     */
    public function assignPermissionToRole(Permission $permission, Role $role): JsonResponse
    {
        $this->rolepermissionService->assignPermissionToRole($permission, $role);
        return $this->success(null, 'Assign permission to role successfully');
    }

    /**
     * Remove permission from role.
     * 
     * @param Permission $permission
     * @param Role $role
     * @return JsonResponse
     */
    public function removePermissionFromRole(Permission $permission, Role $role): JsonResponse
    {
        $this->rolepermissionService->removePermissionFromRole($permission, $role);
        return $this->success(null, 'Remove permission from role successfully');
    }

    /**
     * assign role to a specifice user.
     * 
     * @param Role $role
     * @param User $user
     * @return JsonResponse
     */
    public function assignRoleToUser(Role $role, User $user): JsonResponse
    {
        $this->rolepermissionService->assignRoleToUser($role, $user);
        return $this->success(null, 'Role assigned successfully');
    }

    /**
     * Remove role from user.
     * 
     * @param User $user
     * @param Role $role
     * @return JsonResponse
     */
    public function revokeRoleFromUser(User $user, Role $role): JsonResponse
    {
        $this->rolepermissionService->revokeRoleFromUser($user, $role);
        return $this->success(null, 'Role removed successfully');
    }

    /**
     * Assign permission to user.
     * 
     * @param User $user
     * @param Permission $permission
     * @return JsonResponse
     */
    public function assignPermissionToUser(User $user, Permission $permission): JsonResponse
    {
        $this->rolepermissionService->assignPermissionToUser($user, $permission);
        return $this->success(null, 'Permission assigned successfully');
    }

    /**
     * Remove permission from user.
     * 
     * @param User $user
     * @param Permission $permission
     * @return JsonResponse
     */
    public function revokePermissionFromUser(User $user, Permission $permission): JsonResponse
    {
        $this->rolepermissionService->revokePermissionFromUser($user, $permission);
        return $this->success(null, 'Permission removed successfully');
    }

    /**
     * Check if user has a specific permission.
     * 
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function checkPermission(User $user, Permission $permission)
    {
        return $this->rolepermissionService->checkPermission($user, $permission);
    }

    /**
     * Get all permissions for user.
     * 
     * @param User $user
     * @return JsonResponse
     */
    public function getUserPermissions(User $user)
    {
        $permissions = $this->rolepermissionService->getUserPermissions($user);
        return self::success($permissions, 'Permissions fetched successfully');
    }

}
