<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RegistrationController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

// WebSocket private-channel auth for the Vue client (Bearer token) -> /api/broadcasting/auth
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Authenticated client (Bearer token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/events/{event}/join', [RegistrationController::class, 'store']);
    Route::get('/my-registrations', [RegistrationController::class, 'index']);
    Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy']);
});
