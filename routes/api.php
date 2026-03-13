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

Route::get('/debug-schema', function () {
    try {
        $columns = \DB::select("
            SELECT column_name, data_type
            FROM information_schema.columns
            WHERE table_name = 'announcements'
            ORDER BY ordinal_position
        ");
        $sample = \DB::select("SELECT * FROM announcements LIMIT 1");
        return response()->json(['columns' => $columns, 'sample' => $sample]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/debug-listing', function () {
    try {
        $count = \App\Models\Announcement::count();
        $raw   = \DB::select('SELECT id, title, price, photos, interior_features, exterior_features, other_features, extra_data FROM announcements LIMIT 1');
        try {
            $model = \App\Models\Announcement::first();
            $arr   = $model ? $model->toArray() : null;
            $step  = 'toArray_ok';
        } catch (\Throwable $castErr) {
            $arr  = null;
            $step = 'toArray_failed: ' . $castErr->getMessage() . ' in ' . $castErr->getFile() . ':' . $castErr->getLine();
        }
        return response()->json([
            'count' => $count,
            'raw'   => $raw,
            'model' => $arr,
            'step'  => $step,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'class' => get_class($e),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
        ]);
    }
});

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
    Route::get('/debug-listing', function () {
        try {
            $count = \App\Models\Announcement::count();
            $raw   = \DB::select('SELECT id, title, price, photos, interior_features, exterior_features, other_features, extra_data FROM announcements LIMIT 1');
            try {
                $model = \App\Models\Announcement::first();
                $arr   = $model ? $model->toArray() : null;
                $step  = 'toArray_ok';
            } catch (\Throwable $castErr) {
                $arr  = null;
                $step = 'toArray_failed: ' . $castErr->getMessage() . ' in ' . $castErr->getFile() . ':' . $castErr->getLine();
            }
            return response()->json([
                'count' => $count,
                'raw'   => $raw,
                'model' => $arr,
                'step'  => $step,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
        }
    });

    Route::get('/image-proxy', [App\Http\Controllers\Api\ImageProxyController::class, 'proxy'])
    ->middleware('throttle:200,1');
    // ── Public: Listings ──
    Route::get('/listings/stats', [ListingController::class, 'stats']);
    Route::get('/listings/{id}', [ListingController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/listings', [ListingController::class, 'index']);
    Route::post('/listings', [ListingController::class, 'store'])->middleware('auth:sanctum');

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