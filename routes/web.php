<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
