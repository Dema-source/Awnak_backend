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
     * Assign permission to a user.
     * @param User $user
     * @param Permission $permission
     * @return JsonResponse
     */
    public function assign_permission_to_user(User $user, Permission $permission): JsonResponse
    {
        $this->rolepermissionService->assign_permission_to_user($user, $permission);
        return self::success(null);
    }

    /**
     * Revoke permission to user.
     * @param User $user
     * @param Permission $permission
     * @return JsonResponse
     */
    public function revoke_permission_to_user(User $user, Permission $permission): JsonResponse
    {
        $this->rolepermissionService->revoke_permission_to_user($user, $permission);
        return self::success(null);
    }

    /**
     * Assign permission to a role.
     * @param Permission $permission
     * @param Role $role
     * @return JsonResponse
     */
    public function assign_permission_to_role(Permission $permission, Role $role): JsonResponse
    {
        $this->rolepermissionService->assign_permission_to_role($permission, $role);
        return self::success(null);
    }

    /**
     * assign role to a specifice user.
     * @param Role $role
     * @param User $user
     * @return JsonResponse
     */
    public function assign_role_to_user(Role $role, User $user): JsonResponse
    {
        $this->rolepermissionService->assign_role_to_user($role, $user);
        return self::success(null);
    }

    /**
     * get permissions for user.
     * @param User $user
     * @return JsonResponse
     */
    public function get_user_permissions(User $user)
    {
        $permissions = $this->rolepermissionService->get_user_permissions($user);
        return self::success($permissions);
    }
}
