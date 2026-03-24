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



// Roles & Permissions
// if($user>hasRole('super_admin'))
Route::post('assign-permission-to-role/{permission}/{role}', [RolePermissionController::class, 'assignPermissionToRole']);
Route::post('remove-permission-from-role/{permission}/{role}', [RolePermissionController::class, 'removePermissionFromRole']);

Route::post('assign-role-to-user/{role}/{user}', [RolePermissionController::class, 'assignRoleToUser']);
Route::post('remove-role-from-user/{user}/{role}', [RolePermissionController::class, 'revokeRoleFromUser']);
// (Granting/ٌRevoking) permissions to the user individually.
Route::post('assign-permission-to-user/{user}/{permission}', [RolePermissionController::class, 'assignPermissionToUser']);
Route::post('remove-permission-from-user/{user}/{permission}', [RolePermissionController::class, 'revokePermissionFromUser']);

Route::get('check-permission/{user}/{permission}', [RolePermissionController::class, 'checkPermission']);
Route::get('get-user-permissions/{user}', [RolePermissionController::class, 'getUserPermissions']);

});