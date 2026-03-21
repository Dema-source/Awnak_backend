<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\OrganizationProfileController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskHourController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VolunteerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);


    Route::apiResource('users', UserController::class);
    Route::apiResource('profiles', ProfileController::class);
    Route::apiResource('organizationProfiles', OrganizationProfileController::class);
    Route::apiResource('volunteers', VolunteerController::class);
    Route::apiResource('skills', SkillController::class);
    Route::apiResource('certificates', CertificateController::class);
    Route::apiResource('badges', BadgeController::class);
    Route::apiResource('opportunities', OpportunityController::class);
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('applications', ApplicationController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('taskHours', TaskHourController::class);
    Route::apiResource('taskHours', EvaluationController::class);
    Route::apiResource('taskHours', DocumentController::class);
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
