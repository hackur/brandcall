<?php

use App\Http\Controllers\Api\BrandedCallController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

/*
|--------------------------------------------------------------------------
| Branded Calling API v1
|--------------------------------------------------------------------------
|
| Public API for initiating and managing branded calls.
| Authentication: Bearer token (brand API key)
|
| Endpoints:
|   POST   /api/v1/brands/{slug}/calls          - Initiate a branded call
|   GET    /api/v1/brands/{slug}/calls          - List calls for a brand
|   GET    /api/v1/brands/{slug}/calls/{callId} - Get call status
|
*/

Route::prefix('v1')->group(function () {
    // Branded calls
    Route::prefix('brands/{slug}/calls')->group(function () {
        Route::post('/', [BrandedCallController::class, 'initiate'])->name('api.calls.initiate');
        Route::get('/', [BrandedCallController::class, 'index'])->name('api.calls.index');
        Route::get('/{callId}', [BrandedCallController::class, 'show'])->name('api.calls.show');
    });
});

/*
|--------------------------------------------------------------------------
| Webhooks
|--------------------------------------------------------------------------
|
| Endpoints for receiving webhooks from external services.
| No authentication - signature verification happens in controller.
|
*/

Route::prefix('webhooks')->withoutMiddleware(['throttle:api'])->group(function () {
    Route::post('/numhub', [WebhookController::class, 'numhub'])->name('webhooks.numhub');
    Route::post('/telnyx', [WebhookController::class, 'telnyx'])->name('webhooks.telnyx');
});
