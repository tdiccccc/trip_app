<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ItineraryController;
use App\Http\Controllers\Api\PackingController;
use App\Http\Controllers\Api\SpotController;
use App\Http\Controllers\Api\TripController;
use App\Http\Middleware\EnsureTripMember;
use App\Http\Middleware\EnsureTripOwner;
use Illuminate\Support\Facades\Route;

// 認証不要
Route::post('/login', [AuthController::class, 'login']);

// 認証必要
Route::middleware('auth:sanctum')->group(function (): void {
    // 認証
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // 旅行 CRUD（一覧・作成は全認証ユーザー）
    Route::get('/trips', [TripController::class, 'index']);
    Route::post('/trips', [TripController::class, 'store']);

    // 旅行スコープ（メンバーのみアクセス可）
    Route::prefix('/trips/{tripId}')->middleware(EnsureTripMember::class)->group(function (): void {
        // 旅行詳細
        Route::get('/', [TripController::class, 'show']);

        // owner のみ（更新・削除）
        Route::middleware(EnsureTripOwner::class)->group(function (): void {
            Route::patch('/', [TripController::class, 'update']);
            Route::delete('/', [TripController::class, 'destroy']);
        });

        // しおり（itinerary）
        Route::get('/itinerary', [ItineraryController::class, 'index']);
        Route::post('/itinerary', [ItineraryController::class, 'store']);
        Route::patch('/itinerary/reorder', [ItineraryController::class, 'reorder']);
        Route::patch('/itinerary/{id}', [ItineraryController::class, 'update']);
        Route::delete('/itinerary/{id}', [ItineraryController::class, 'destroy']);

        // スポット
        Route::get('/spots', [SpotController::class, 'index']);
        Route::get('/spots/{id}', [SpotController::class, 'show']);
        Route::post('/spots/{id}/memos', [SpotController::class, 'storeMemo']);
        Route::get('/spots/{id}/photos', [SpotController::class, 'photos']);

        // アルバム（写真）
        Route::get('/photos', [AlbumController::class, 'index']);
        Route::post('/photos', [AlbumController::class, 'store']);
        Route::delete('/photos/{id}', [AlbumController::class, 'destroy']);

        // 掲示板
        Route::get('/board', [BoardController::class, 'index']);
        Route::post('/board', [BoardController::class, 'store']);
        Route::post('/board/{id}/reactions', [BoardController::class, 'storeReaction']);

        // パッキングリスト
        Route::get('/packing', [PackingController::class, 'index']);
        Route::post('/packing', [PackingController::class, 'store']);
        Route::patch('/packing/{id}', [PackingController::class, 'update']);
        Route::delete('/packing/{id}', [PackingController::class, 'destroy']);

        // 費用メモ
        Route::get('/expenses/summary', [ExpenseController::class, 'summary']);
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

        // エクスポート
        Route::post('/export/itinerary-pdf', [ExportController::class, 'itineraryPdf']);
        Route::post('/export/photobook-pdf', [ExportController::class, 'photobookPdf']);
        Route::post('/export/slideshow-video', [ExportController::class, 'slideshowVideo']);
        Route::post('/export/zip', [ExportController::class, 'zip']);
    });
});

// ヘルスチェック
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
