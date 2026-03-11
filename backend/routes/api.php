<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItineraryController;
use App\Http\Controllers\Api\SpotController;
use Illuminate\Support\Facades\Route;

// 認証不要
Route::post('/login', [AuthController::class, 'login']);

// 認証必要
Route::middleware('auth:sanctum')->group(function (): void {
    // 認証
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // スポット
    Route::get('/spots', [SpotController::class, 'index']);
    Route::get('/spots/{id}', [SpotController::class, 'show']);
    Route::post('/spots/{id}/memos', [SpotController::class, 'storeMemo']);
    Route::get('/spots/{id}/photos', [SpotController::class, 'photos']);

    // しおり（itinerary）
    Route::get('/itinerary', [ItineraryController::class, 'index']);
    Route::post('/itinerary', [ItineraryController::class, 'store']);
    Route::patch('/itinerary/reorder', [ItineraryController::class, 'reorder']);
    Route::patch('/itinerary/{id}', [ItineraryController::class, 'update']);
    Route::delete('/itinerary/{id}', [ItineraryController::class, 'destroy']);

    // アルバム
    Route::get('/photos', [AlbumController::class, 'index']);
    Route::post('/photos', [AlbumController::class, 'store']);
    Route::delete('/photos/{id}', [AlbumController::class, 'destroy']);
});

// ヘルスチェック
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
