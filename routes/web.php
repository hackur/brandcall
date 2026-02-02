<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Landing Pages (Dynamic, slug-based)
|--------------------------------------------------------------------------
*/

Route::get('/lp/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show');
Route::post('/lp/{slug}/lead', [LandingPageController::class, 'submitLead'])->name('landing-page.lead');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Customer Dashboard - Inertia/React)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Brands
    Route::resource('brands', BrandController::class);

    // Call logs (placeholder)
    Route::get('/calls', function () {
        return Inertia::render('Calls/Index', ['calls' => []]);
    })->name('calls.index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Handled by Filament at /admin
|--------------------------------------------------------------------------
| See: app/Providers/Filament/AdminPanelProvider.php
| Access controlled by User::canAccessPanel() which checks for super-admin role
*/

/*
|--------------------------------------------------------------------------
| Health Check Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/health', HealthCheckResultsController::class)->name('health');
    Route::get('/health/json', HealthCheckJsonResultsController::class)->name('health.json');
});

/*
|--------------------------------------------------------------------------
| Pulse Dashboard (Monitoring)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:viewPulse'])->prefix('pulse')->group(function () {
    Route::get('/', function () {
        return view('vendor.pulse.dashboard');
    })->name('pulse');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
