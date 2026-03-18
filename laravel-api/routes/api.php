<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminArticleController;
use App\Http\Controllers\Api\TranslationController;
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

    // ── Public: Field labels (translated by locale) ──
    Route::get('/labels', fn () => response()->json(trans('listing')));

    // ── Public: Regions & Cities ──
    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/cities', [RegionController::class, 'cities']);

    // ── Public: Ads ──
    Route::get('/ads', [AdController::class, 'index']);
    Route::get('/ads/{id}', [AdController::class, 'show'])->where('id', '[0-9]+');

    // ── Protected: Ads (create / update / delete) ──
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/ads', [AdController::class, 'store']);
        Route::put('/ads/{id}', [AdController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/ads/{id}', [AdController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // ── Auth: Public ──
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // ── Auth: Protected ──
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
    });

    // ── Public: Articles ──
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);

    // ── Admin: Auth ──
    Route::post('/admin/login', [AdminAuthController::class, 'login']);

    // ── Admin: Protected ──
    Route::middleware('auth:admin')->prefix('admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me', [AdminAuthController::class, 'me']);

        // Articles CRUD
        Route::get('/articles', [AdminArticleController::class, 'index']);
        Route::post('/articles', [AdminArticleController::class, 'store']);
        Route::get('/articles/{id}', [AdminArticleController::class, 'show']);
        Route::put('/articles/{id}', [AdminArticleController::class, 'update']);
        Route::delete('/articles/{id}', [AdminArticleController::class, 'destroy']);

        // Translations
        Route::post('/translations/push', [TranslationController::class, 'push']);
        Route::get('/translations/pending', [TranslationController::class, 'pending']);
    });
});