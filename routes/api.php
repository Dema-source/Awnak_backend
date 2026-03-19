<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RolePermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});



// Roles & Permissions
// isAdmin middleware
Route::post('assign-permission-to-user', [RolePermissionController::class, 'assign_permission_to_user']);
Route::post('revoke-permission-to-user', [RolePermissionController::class, 'revoke_permission_to_user']);

Route::post('assign-permission-to-role', [RolePermissionController::class, 'assign_permission_to_role']);
// Route::post('revoke-permission-to-role', [RolePermissionController::class, 'revoke_permission_to_role']);

Route::post('assign-role-to-user', [RolePermissionController::class, 'assign_role_to_user']);
// Route::post('revoke-role-to-user', [RolePermissionController::class, 'revoke_role_to_user']);

Route::get('get-user-permissions', [RolePermissionController::class, 'get_user_permissions']);
