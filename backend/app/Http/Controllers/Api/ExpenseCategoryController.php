<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Packages\Application\UseCases\ExpenseCategory\CreateExpenseCategoryUseCase;
use Packages\Application\UseCases\ExpenseCategory\DeleteExpenseCategoryUseCase;
use Packages\Application\UseCases\ExpenseCategory\GetExpenseCategoriesUseCase;
use Packages\Application\UseCases\ExpenseCategory\UpdateExpenseCategoryUseCase;
use Packages\Domain\Exceptions\CategoryInUseException;
use Packages\Domain\Exceptions\DomainException;

final class ExpenseCategoryController extends Controller
{
    /**
     * GET /api/trips/{tripId}/expense-categories
     */
    public function index(int $tripId, GetExpenseCategoriesUseCase $useCase): JsonResponse
    {
        $categories = $useCase->execute($tripId);

        return response()->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $categories),
        ]);
    }

    /**
     * POST /api/trips/{tripId}/expense-categories
     */
    public function store(int $tripId, StoreExpenseCategoryRequest $request, CreateExpenseCategoryUseCase $useCase): JsonResponse
    {
        $validated = $request->validated();

        try {
            $category = $useCase->execute(
                tripId: $tripId,
                name: $validated['name'],
                key: $validated['key'] ?? $this->generateKey($validated['name']),
                color: $validated['color'] ?? null,
                sortOrder: (int) ($validated['sort_order'] ?? 0),
            );
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['data' => $category->toArray()], 201);
    }

    /**
     * PUT /api/trips/{tripId}/expense-categories/{id}
     */
    public function update(int $tripId, int $id, UpdateExpenseCategoryRequest $request, UpdateExpenseCategoryUseCase $useCase): JsonResponse
    {
        try {
            $category = $useCase->execute($id, $request->validated());
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(['data' => $category->toArray()]);
    }

    /**
     * DELETE /api/trips/{tripId}/expense-categories/{id}
     */
    public function destroy(int $tripId, int $id, DeleteExpenseCategoryUseCase $useCase): JsonResponse|Response
    {
        try {
            $useCase->execute($id);
        } catch (CategoryInUseException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->noContent();
    }

    /**
     * 名前からキーを自動生成する（簡易的なスラッグ化）
     */
    private function generateKey(string $name): string
    {
        $key = strtolower(trim($name));
        $key = (string) preg_replace('/[^a-z0-9]+/', '-', $key);
        $key = trim($key, '-');

        return $key !== '' ? $key : 'custom-' . time();
    }
}
