<?php

use App\Http\Controllers\Admin\DocumentDownloadController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OnboardingController;
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

Route::get('/features', fn() => Inertia::render('Features'))->name('features');
Route::get('/solutions', fn() => Inertia::render('Solutions'))->name('solutions');
Route::get('/pricing', fn() => Inertia::render('Pricing'))->name('pricing');
Route::get('/compliance', fn() => Inertia::render('Compliance'))->name('compliance');

/*
|--------------------------------------------------------------------------
| Marketing Pages (Public Content)
|--------------------------------------------------------------------------
*/

Route::get('/features', function () {
    return Inertia::render('Features');
})->name('features');

Route::get('/solutions', function () {
    return Inertia::render('Solutions');
})->name('solutions');

Route::get('/pricing', function () {
    return Inertia::render('Pricing');
})->name('pricing');

Route::get('/compliance', function () {
    return Inertia::render('Compliance');
})->name('compliance');

Route::get('/guide/branded-calling', function () {
    return Inertia::render('Guide/BrandedCallingGuide');
})->name('guide.branded-calling');

/*
|--------------------------------------------------------------------------
| Landing Pages (Dynamic, slug-based)
|--------------------------------------------------------------------------
*/

Route::get('/lp/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show');
Route::post('/lp/{slug}/lead', [LandingPageController::class, 'submitLead'])->name('landing-page.lead');

/*
|--------------------------------------------------------------------------
| Onboarding Routes (Authenticated, but doesn't require verified email)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('index');
    Route::get('/profile', [OnboardingController::class, 'profile'])->name('profile');
    Route::post('/profile', [OnboardingController::class, 'updateProfile'])->name('profile.update');
    Route::get('/documents', [OnboardingController::class, 'documents'])->name('documents');
    Route::post('/documents', [OnboardingController::class, 'uploadDocument'])->name('documents.upload');
    Route::delete('/documents/{document}', [OnboardingController::class, 'deleteDocument'])->name('documents.delete');
    Route::get('/documents/{document}/view', [OnboardingController::class, 'viewDocument'])->name('documents.view');
    Route::post('/kyc/submit', [OnboardingController::class, 'submitKyc'])->name('kyc.submit');
    Route::get('/tickets', [OnboardingController::class, 'tickets'])->name('tickets');
    Route::post('/tickets', [OnboardingController::class, 'createTicket'])->name('tickets.create');
    Route::post('/tickets/{ticket}/reply', [OnboardingController::class, 'replyToTicket'])->name('tickets.reply');
    Route::get('/settings', [OnboardingController::class, 'settings'])->name('settings');
    Route::get('/docs', [OnboardingController::class, 'documentation'])->name('documentation');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Customer Dashboard - Requires verified & approved)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (redirects to onboarding if not approved)
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
| Health Check Routes (super-admin only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:viewHealth'])->group(function () {
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
| Admin Document Download
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:viewAny,App\Models\Document'])
    ->get('/admin/documents/{document}/download', DocumentDownloadController::class)
    ->name('filament.admin.documents.download');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
