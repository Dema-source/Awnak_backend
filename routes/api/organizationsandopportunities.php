<?php

use App\Http\Controllers\Api\OrganizationProfileController;
use App\Http\Controllers\Api\RolesPermissions\RoleController;
use App\Http\Controllers\Api\RolesPermissions\RolePermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Organizations & Opportunities API Routes
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

    // Get all ACTIVE organizations
    Route::get('organizationProfiles', [OrganizationProfileController::class, 'index']);

    Route::get('organizationProfiles/{id}', [OrganizationProfileController::class, 'show'])
        ->middleware('permission:users.read');

    // Create a new organization
    Route::post('organizationProfiles', [OrganizationProfileController::class, 'store']);

    Route::put('organizationProfiles/{id}', [OrganizationProfileController::class, 'update']);

    Route::delete('organizationProfiles/{id}', [OrganizationProfileController::class, 'destroy'])
        ->middleware('role:system_admin');

        // Get all NOT ACTIVE organizations
    Route::get('organizationProfiles/notactive', [OrganizationProfileController::class, 'listNotActive'])
        ->middleware('role:system_admin');
});
