<?php

use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\BookingController;
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

// Logged-in users (Customers & Admins)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Booking APIs
    Route::get('/bookings', [BookingController::class, 'myBookings'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

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

        // Admin Booking Management APIs
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    });
