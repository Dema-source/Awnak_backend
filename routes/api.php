<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\OrganizationProfileController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\VolunteerCertificateController;
use App\Http\Controllers\Api\VolunteerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth
// require __DIR__ . '/api/auth.php';

// // User Management
// require __DIR__ . '/api/users.php';

// // Roles & Permissions
// require __DIR__ . '/api/rolesandpermissions.php';

// // Organizations & Opportunities
// require __DIR__ . '/api/organizationsandopportunities.php';





Route::middleware('auth:sanctum')->group(function () {

    // Super Administrator routes - full access
    Route::prefix('admin')->middleware('role:super_administrator')->group(function () {
        Route::apiResource('profiles', ProfileController::class);
        Route::apiResource('volunteers', VolunteerController::class);
        Route::put('volunteer/{volunteer}/status', [VolunteerController::class, 'updateStatus']);
        Route::apiResource('skills', SkillController::class);
        Route::apiResource('opportunities', OpportunityController::class);
        Route::apiResource('locations', LocationController::class);
        Route::post('opportunities/{opportunity}/locations', [LocationController::class, 'store']);
        Route::apiResource('applications', ApplicationController::class);
        Route::post('opportunities/{opportunity}/volunteers', [ApplicationController::class, 'store']);
        Route::apiResource('tasks', TaskController::class);
        Route::post('opportunity/{opportunity}/task', [TaskController::class, 'store']);
        Route::put('task/{task}/status', [TaskController::class, 'updateStatus']);
        Route::apiResource('certificates', CertificateController::class);
        Route::apiResource('volunteer_certificates', VolunteerCertificateController::class);
        Route::post('volunteer_certificates/{task}', [VolunteerCertificateController::class, 'store']);
        Route::apiResource('badges', BadgeController::class);
        Route::apiResource('evaluations', EvaluationController::class);
    });

    // System Admin routes - administrative access
    Route::prefix('system')->middleware('role:system_admin')->group(function () {
        Route::apiResource('profiles', ProfileController::class);
        Route::apiResource('volunteers', VolunteerController::class);
        Route::put('volunteer/{volunteer}/status', [VolunteerController::class, 'updateStatus']);
        Route::apiResource('skills', SkillController::class);
        Route::apiResource('opportunities', OpportunityController::class);
        Route::apiResource('locations', LocationController::class)->except(['store']);
        Route::post('opportunities/{opportunity}/locations', [LocationController::class, 'store']);
        Route::apiResource('applications', ApplicationController::class)->except(['store']);
        Route::post('opportunities/{opportunity}/volunteers', [ApplicationController::class, 'store']);
        Route::apiResource('tasks', TaskController::class)->except(['store']);
        Route::post('opportunity/{opportunity}/task', [TaskController::class, 'store']);
        Route::put('task/{task}/status', [TaskController::class, 'updateStatus']);
        Route::apiResource('certificates', CertificateController::class);
        Route::apiResource('volunteer_certificates', VolunteerCertificateController::class)->except(['store']);
        Route::post('volunteer_certificates/{task}', [VolunteerCertificateController::class, 'store']);
        Route::apiResource('badges', BadgeController::class);
        Route::apiResource('evaluations', EvaluationController::class);
    });

    // Organization Admin routes - organization management
    Route::prefix('organization')->middleware('role:organization_admin')->group(function () {
        Route::apiResource('opportunities', OpportunityController::class);
        Route::apiResource('locations', LocationController::class)->except(['store']);
        Route::post('opportunities/{opportunity}/locations', [LocationController::class, 'store']);
        Route::apiResource('applications', ApplicationController::class)->only(['index', 'show', 'update']);
        Route::post('opportunities/{opportunity}/volunteers', [ApplicationController::class, 'store']);
        Route::apiResource('tasks', TaskController::class)->only(['index', 'store', 'show', 'update']);
        Route::post('opportunity/{opportunity}/task', [TaskController::class, 'store']);
        Route::put('task/{task}/status', [TaskController::class, 'updateStatus']);
        Route::apiResource('volunteer_certificates', VolunteerCertificateController::class)->only(['index', 'show', 'store']);
        Route::post('volunteer_certificates/{task}', [VolunteerCertificateController::class, 'store']);
        // Cannot delete applications
    });

    // Opportunity Manager routes - opportunity management
    Route::prefix('opportunity')->middleware('role:opportunity_manager')->group(function () {
        Route::apiResource('opportunities', OpportunityController::class);
        Route::apiResource('locations', LocationController::class)->only(['index', 'show']);
        Route::post('opportunities/{opportunity}/locations', [LocationController::class, 'store']);
        Route::apiResource('applications', ApplicationController::class)->only(['index', 'show', 'update']);
        Route::post('opportunities/{opportunity}/volunteers', [ApplicationController::class, 'store']);
        Route::apiResource('tasks', TaskController::class)->only(['index', 'store', 'show', 'update']);
        Route::post('opportunity/{opportunity}/task', [TaskController::class, 'store']);
        Route::put('task/{task}/status', [TaskController::class, 'updateStatus']);
        // Cannot delete locations or applications
    });

    // Volunteer Coordinator routes - volunteer management
    Route::prefix('coordinator')->middleware('role:volunteer_coordinator')->group(function () {
        Route::apiResource('volunteers', VolunteerController::class)->only(['index', 'show', 'update']);
        Route::put('volunteer/{volunteer}/status', [VolunteerController::class, 'updateStatus']);
        Route::apiResource('applications', ApplicationController::class)->only(['index', 'show', 'update']);
        Route::apiResource('tasks', TaskController::class)->only(['index', 'show', 'update']);
        Route::put('task/{task}/status', [TaskController::class, 'updateStatus']);
        Route::apiResource('volunteer_certificates', VolunteerCertificateController::class)->only(['index', 'show']);
        Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);
    });

    // Performance Evaluator routes - evaluation management
    Route::prefix('evaluator')->middleware('role:performance_evaluator')->group(function () {
        Route::apiResource('evaluations', EvaluationController::class);
        Route::apiResource('tasks', TaskController::class)->only(['index', 'show']);
        Route::apiResource('volunteers', VolunteerController::class)->only(['index', 'show']);
        Route::apiResource('volunteer_certificates', VolunteerCertificateController::class)->only(['index', 'show']);
    });

    // Volunteer routes - basic volunteer access
    Route::prefix('volunteer')->middleware('role:volunteer')->group(function () {
        Route::apiResource('opportunities', OpportunityController::class)->only(['index', 'show']);
        Route::apiResource('applications', ApplicationController::class)->only(['index', 'store', 'show']);
        Route::post('opportunities/{opportunity}/volunteers', [ApplicationController::class, 'store']);
        Route::apiResource('tasks', TaskController::class)->only(['index', 'show']);
        Route::apiResource('volunteer_certificates', VolunteerCertificateController::class)->only(['index', 'show']);
        Route::apiResource('badges', BadgeController::class)->only(['index', 'show']);
        Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
        // Volunteers can manage their own profile
        Route::apiResource('profiles', ProfileController::class)->only(['show', 'update']);
        // Volunteers cannot create or delete resources
        Route::apiResource('evaluations', EvaluationController::class)->only(['index', 'show']);
    });

});


require __DIR__.'/auth.php';