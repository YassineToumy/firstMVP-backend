<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImageProxyController;
/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    try {
        \DB::connection()->getPdo();
        $dbOk = true;
        $dbError = null;
    } catch (\Exception $e) {
        $dbOk = false;
        $dbError = $e->getMessage();
    }
    return response()->json([
        'status' => $dbOk ? 'ok' : 'error',
        'db'     => $dbOk ? 'connected' : $dbError,
        'php'    => PHP_VERSION,
        'env'    => config('app.env'),
    ]);
});

Route::prefix('v1')->group(function () {
    Route::get('/image-proxy', [App\Http\Controllers\Api\ImageProxyController::class, 'proxy'])
    ->middleware('throttle:200,1'); // 200 requests per minute max
    // ── Public: Listings ──
    Route::get('/listings/stats', [ListingController::class, 'stats']);
    Route::get('/listings/{id}', [ListingController::class, 'show'])->where('id', '[0-9]+');
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