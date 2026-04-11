<?php

use App\Http\Controllers\Api\RolesPermissions\RoleController;
use App\Http\Controllers\Api\RolesPermissions\RolePermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Roles & Permissions API Routes
|--------------------------------------------------------------------------
| API: {{baseURL}}/api/admin/roles
| API: {{baseURL}}/api/admin/assign-permission-to-role
| API: {{baseURL}}/api/admin/remove-permission-from-role
| API: {{baseURL}}/api/admin/assign-role-to-user
| API: {{baseURL}}/api/admin/remove-role-from-user
| API: {{baseURL}}/api/admin/assign-permission-to-user
| API: {{baseURL}}/api/admin/remove-permission-from-user
| API: {{baseURL}}/api/admin/check-permission
| API: {{baseURL}}/api/admin/get-user-permissions/{user}
| Middleware: auth:sanctum
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('roles', RoleController::class);

    Route::post('assign-permission-to-role', [RolePermissionController::class, 'assignPermissionToRole']);

    Route::post('remove-permission-from-role', [RolePermissionController::class, 'removePermissionFromRole']);

    Route::post('assign-role-to-user', [RolePermissionController::class, 'assignRoleToUser']);

    Route::post('remove-role-from-user', [RolePermissionController::class, 'revokeRoleFromUser']);

    Route::post('assign-permission-to-user', [RolePermissionController::class, 'assignPermissionToUser']);

    Route::post('remove-permission-from-user', [RolePermissionController::class, 'revokePermissionFromUser']);

    Route::get('check-permission', [RolePermissionController::class, 'checkPermission']);

    Route::get('get-user-permissions/{user}', [RolePermissionController::class, 'getUserPermissions']);
});
