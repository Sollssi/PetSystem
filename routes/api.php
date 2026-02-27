<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\VaccinationController;
use App\Http\Controllers\Api\PasswordResetController;

Route::get('/ping', fn() => response()->json([
    'success' => true,
    'data' => ['status' => 'ok'],
    'message' => 'API is running correctly'
]));

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [PasswordResetController::class, 'forgot']);
Route::post('/password/reset', [PasswordResetController::class, 'reset']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::apiResource('pets', PetController::class);
        Route::apiResource('appointments', AppointmentController::class);

        Route::get('/pets/{pet}/vaccinations', [VaccinationController::class, 'index'])->name('vaccinations.index');
        Route::post('/pets/{pet}/vaccinations', [VaccinationController::class, 'store'])->name('vaccinations.store');
        Route::delete('/pets/{pet}/vaccinations/{record}', [VaccinationController::class, 'destroy'])->name('vaccinations.destroy');
    });
});
