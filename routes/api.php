<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
*/
// Add this to your routes/api.php inside the v1 group


Route::prefix('v1')->group(function () {
    Route::get('/v1/image-proxy', [App\Http\Controllers\Api\ImageProxyController::class, 'proxy'])
    ->middleware('throttle:200,1'); // 200 requests per minute max
    // ── Public: Listings ──
    Route::get('/listings/stats', [ListingController::class, 'stats']);
    Route::get('/listings/{source}/{id}', [ListingController::class, 'show']);
    Route::get('/listings', [ListingController::class, 'index']);

    // ── Public: Regions & Cities ──
    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/cities', [RegionController::class, 'cities']);

    // ── Auth: Public ──
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // ── Auth: Protected ──
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
    });
});