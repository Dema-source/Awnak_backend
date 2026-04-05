<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Users API Routes
|--------------------------------------------------------------------------
| API: {{baseURL}}/api/users
| Middleware: auth:sanctum
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission:users.read');

    Route::post('users', [UserController::class, 'store'])
        ->middleware('permission:users.create');

    Route::put('users/{id}', [UserController::class, 'update'])
        ->middleware('permission:users.update');

    Route::delete('users/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete');
});
