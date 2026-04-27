<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {

    // Super Administrator routes - full access
    Route::prefix('admin')->middleware('role:super_administrator')->group(function () {
        require __DIR__ . '/api/super_administrator.php';
    });

    // System Admin routes - administrative access
    Route::prefix('system')->middleware(['role:system_admin', 'user.active'])->group(function () {
        require __DIR__ . '/api/system_admin.php';
    });

    // Organization Admin routes - organization management
    Route::prefix('organization')->middleware(['role:organization_admin', 'user.active', 'organization.profile.active'])->group(function () {
        require __DIR__ . '/api/organization_admin.php';
    });

    // Opportunity Manager routes - opportunity management
    Route::prefix('opportunity')->middleware(['role:opportunity_manager', 'user.active'])->group(function () {
        require __DIR__ . '/api/opportunity_manager.php';
    });

    // Volunteer Coordinator routes - volunteer management
    Route::prefix('coordinator')->middleware(['role:volunteer_coordinator', 'user.active'])->group(function () {
        require __DIR__ . '/api/volunteer_coordinator.php';
    });

    // Performance Evaluator routes - evaluation management
    Route::prefix('evaluator')->middleware(['role:performance_evaluator', 'user.active'])->group(function () {
        require __DIR__ . '/api/performance_evaluator.php';
    });

    // Volunteer routes - basic volunteer access
    Route::prefix('volunteer')->middleware(['role:volunteer', 'user.active'])->group(function () {
        require __DIR__ . '/api/volunteer.php';
    });
});


require __DIR__ . '/auth.php';
