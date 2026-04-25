<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OrganizationAdminRegistrationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\StaffRegistrationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\VolunteerRegistrationController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest'])
    ->name('register');

// Organization Admin Registration
Route::prefix('organization')->group(function () {
    Route::post('/register', [OrganizationAdminRegistrationController::class, 'register'])
        ->middleware(['guest'])
        ->name('organization-admin.register');
    
    Route::get('/requirements', [OrganizationAdminRegistrationController::class, 'requirements'])
        ->middleware(['guest'])
        ->name('organization-admin.requirements');
});

// Volunteer Registration
Route::prefix('volunteer')->group(function () {
    Route::post('/register', [VolunteerRegistrationController::class, 'register'])
        ->middleware(['guest'])
        ->name('volunteer.register');
    
    Route::get('/requirements', [VolunteerRegistrationController::class, 'requirements'])
        ->middleware(['guest'])
        ->name('volunteer.requirements');
});

// Staff Registration
Route::prefix('staff')->group(function () {
    Route::post('/register', [StaffRegistrationController::class, 'register'])
        ->middleware(['guest'])
        ->name('staff.register');
    
    Route::get('/requirements', [StaffRegistrationController::class, 'requirements'])
        ->middleware(['guest'])
        ->name('staff.requirements');
});

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest'])
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
