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
use Packages\Domain\Repositories\ExpenseRepositoryInterface;

final class ExpenseController extends Controller
{
    /**
     * GET /api/expenses
     */
    public function index(Request $request, ExpenseRepositoryInterface $expenseRepository): JsonResponse
    {
        $category = $request->query('category');
        $categoryStr = is_string($category) ? $category : null;

        $expenses = $expenseRepository->findAll($categoryStr);

        $data = array_map(
            fn ($expense) => ExpenseDto::fromEntity($expense)->toArray(),
            $expenses,
        );

        return response()->json(['data' => $data]);
    }

    /**
     * POST /api/expenses
     */
    public function store(StoreExpenseRequest $request, RecordExpenseUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        $expense = $useCase->execute(
            userId: $user->id,
            description: $validated['description'],
            amount: (int) $validated['amount'],
            category: $validated['category'] ?? 'other',
            paidAt: $validated['paid_at'],
            isShared: (bool) ($validated['is_shared'] ?? true),
        );

        return response()->json(['data' => $expense->toArray()], 201);
    }

    /**
     * DELETE /api/expenses/{id}
     */
    public function destroy(int $id, DeleteExpenseUseCase $useCase): Response
    {
        $useCase->execute($id);

        return response()->noContent();
    }

    /**
     * GET /api/expenses/summary
     */
    public function summary(GetExpenseSummaryUseCase $useCase): JsonResponse
    {
        $summary = $useCase->execute();

        return response()->json(['data' => $summary]);
    }
}
