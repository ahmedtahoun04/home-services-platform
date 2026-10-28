<?php

use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

// Public, read-only catalog. Served under the /api prefix.
Route::get('/services', [ServiceController::class, 'index'])->name('api.services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('api.services.show');
