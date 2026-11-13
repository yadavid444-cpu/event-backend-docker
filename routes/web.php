<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('events', EventController::class);

        Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');
        Route::patch('/registrations/{registration}/approve', [RegistrationController::class, 'approve'])->name('registrations.approve');
        Route::patch('/registrations/{registration}/reject', [RegistrationController::class, 'reject'])->name('registrations.reject');
    });
});
