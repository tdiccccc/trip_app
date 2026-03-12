<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Packages\Application\UseCases\Master\GetAssigneesUseCase;
use Packages\Application\UseCases\Master\GetExpenseCategoriesUseCase;

final class MasterController extends Controller
{
    /**
     * GET /api/master/expense-categories?trip_id={tripId}
     *
     * 費用カテゴリの一覧を返す。
     *
     * @deprecated 旅行スコープの /api/trips/{tripId}/expense-categories を使用してください。
     */
    public function expenseCategories(Request $request, GetExpenseCategoriesUseCase $useCase): JsonResponse
    {
        $tripId = $request->query('trip_id');

        if (! is_numeric($tripId)) {
            return response()->json(['message' => 'trip_id is required.'], 422);
        }

        return response()->json([
            'data' => $useCase->execute((int) $tripId),
        ]);
    }

    /**
     * GET /api/master/assignees
     *
     * 担当者区分の一覧を返す。
     */
    public function assignees(GetAssigneesUseCase $useCase): JsonResponse
    {
        return response()->json([
            'data' => $useCase->execute(),
        ]);
    }
}
