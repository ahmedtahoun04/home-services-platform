<?php

use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Guests only
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logged-in users only
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Administrators only: must be logged in AND have the admin role
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard', [
            'serviceCount' => Service::count(),
        ]))->name('dashboard');

        // GET/POST /admin/services, GET/PUT/PATCH/DELETE /admin/services/{service}
        Route::apiResource('services', AdminServiceController::class);
    });
