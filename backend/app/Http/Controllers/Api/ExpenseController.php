<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Packages\Application\DTOs\ExpenseDto;
use Packages\Application\UseCases\Expense\DeleteExpenseUseCase;
use Packages\Application\UseCases\Expense\GetExpenseSummaryUseCase;
use Packages\Application\UseCases\Expense\RecordExpenseUseCase;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use Packages\Domain\Repositories\ExpenseRepositoryInterface;

final class ExpenseController extends Controller
{
    /**
     * GET /api/trips/{tripId}/expenses
     */
    public function index(
        int $tripId,
        Request $request,
        ExpenseRepositoryInterface $expenseRepository,
        ExpenseCategoryRepositoryInterface $expenseCategoryRepository,
    ): JsonResponse {
        $categoryId = $request->query('category_id');
        $categoryIdInt = is_numeric($categoryId) ? (int) $categoryId : null;

        $expenses = $expenseRepository->findAll($tripId, $categoryIdInt);

        // カテゴリマップを構築
        $categories = $expenseCategoryRepository->findAll($tripId);
        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[$category->id] = $category;
        }

        $data = array_map(
            fn ($expense) => ExpenseDto::fromEntity($expense, $categoryMap[$expense->categoryId] ?? null)->toArray(),
            $expenses,
        );

        return response()->json(['data' => $data]);
    }

    /**
     * POST /api/trips/{tripId}/expenses
     */
    public function store(int $tripId, StoreExpenseRequest $request, RecordExpenseUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        $expense = $useCase->execute(
            tripId: $tripId,
            userId: $user->id,
            description: $validated['description'],
            amount: (int) $validated['amount'],
            categoryId: (int) $validated['expense_category_id'],
            paidAt: $validated['paid_at'],
            isShared: (bool) ($validated['is_shared'] ?? true),
        );

        return response()->json(['data' => $expense->toArray()], 201);
    }

    /**
     * DELETE /api/trips/{tripId}/expenses/{id}
     */
    public function destroy(int $tripId, int $id, DeleteExpenseUseCase $useCase): Response
    {
        $useCase->execute($tripId, $id);

        return response()->noContent();
    }

    /**
     * GET /api/trips/{tripId}/expenses/summary
     */
    public function summary(int $tripId, GetExpenseSummaryUseCase $useCase): JsonResponse
    {
        $summary = $useCase->execute($tripId);

        return response()->json(['data' => $summary]);
    }
}
