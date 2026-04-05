<?php

use App\Http\Controllers\Api\RolesPermissions\RoleController;
use App\Http\Controllers\Api\RolesPermissions\RolePermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Roles API Routes
|--------------------------------------------------------------------------
| API: {{baseURL}}/api/roles
| API: {{baseURL}}/api/assign-permission-to-role
| API: {{baseURL}}/api/remove-permission-from-role
| API: {{baseURL}}/api/assign-role-to-user
| API: {{baseURL}}/api/remove-role-from-user
| API: {{baseURL}}/api/assign-permission-to-user
| API: {{baseURL}}/api/remove-permission-from-user
| API: {{baseURL}}/api/check-permission
| API: {{baseURL}}/api/get-user-permissions/{user}
| Middleware: auth:sanctum
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('roles', RoleController::class);

    Route::post('assign-permission-to-role', [RolePermissionController::class, 'assignPermissionToRole'])
        ->middleware('permission:permissions.update');

    Route::post('remove-permission-from-role', [RolePermissionController::class, 'removePermissionFromRole'])
        ->middleware('permission:permissions.update');

    Route::post('assign-role-to-user', [RolePermissionController::class, 'assignRoleToUser'])
        ->middleware('permission:roles.update');

    Route::post('remove-role-from-user', [RolePermissionController::class, 'revokeRoleFromUser'])
        ->middleware('permission:roles.update');

    Route::post('assign-permission-to-user', [RolePermissionController::class, 'assignPermissionToUser'])
        ->middleware('permission:permissions.update');

    Route::post('remove-permission-from-user', [RolePermissionController::class, 'revokePermissionFromUser'])
        ->middleware('permission:permissions.update');

    Route::get('check-permission', [RolePermissionController::class, 'checkPermission'])
        ->middleware('permission:permissions.check');

    Route::get('get-user-permissions/{user}', [RolePermissionController::class, 'getUserPermissions'])
        ->middleware('permission:permissions.read');
});
