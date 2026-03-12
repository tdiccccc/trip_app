<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Packages\Application\UseCases\Master\GetAssigneesUseCase;
use Packages\Application\UseCases\Master\GetExpenseCategoriesUseCase;

final class MasterController extends Controller
{
    /**
     * GET /api/master/expense-categories
     *
     * 費用カテゴリの一覧を返す。
     */
    public function expenseCategories(GetExpenseCategoriesUseCase $useCase): JsonResponse
    {
        return response()->json([
            'data' => $useCase->execute(),
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
