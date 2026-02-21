<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest-only routes (redirect to dashboard if already logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout (auth users only)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/fleet/{vehicle}/detail', [DashboardController::class, 'fleetDetail'])->name('dashboard.fleet.detail');

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/toggle-status', [VehicleController::class, 'toggleStatus'])->name('vehicles.toggle-status');

    Route::resource('drivers', DriverController::class);

    Route::resource('trips', TripController::class);
    Route::post('trips/{trip}/dispatch', [TripController::class, 'dispatch'])->name('trips.dispatch');
    Route::post('trips/{trip}/complete', [TripController::class, 'complete'])->name('trips.complete');
    Route::post('trips/{trip}/cancel', [TripController::class, 'cancel'])->name('trips.cancel');

    Route::resource('maintenance', MaintenanceController::class);

    Route::resource('fuel', FuelController::class);

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // API Routes for AJAX
    Route::middleware('auth')->prefix('api')->group(function () {
        Route::get('/dashboard/refresh', [App\Http\Controllers\API\DashboardController::class, 'refresh'])->name('api.dashboard.refresh');
    });
});
