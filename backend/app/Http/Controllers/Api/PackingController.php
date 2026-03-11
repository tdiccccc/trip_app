<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackingItemRequest;
use App\Http\Requests\UpdatePackingItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Packages\Application\UseCases\Packing\CreatePackingItemUseCase;
use Packages\Application\UseCases\Packing\DeletePackingItemUseCase;
use Packages\Application\UseCases\Packing\GetPackingListUseCase;
use Packages\Application\UseCases\Packing\UpdatePackingItemUseCase;

final class PackingController extends Controller
{
    /**
     * GET /api/trips/{tripId}/packing
     */
    public function index(int $tripId, Request $request, GetPackingListUseCase $useCase): JsonResponse
    {
        $assignee = $request->query('assignee');
        $assigneeStr = is_string($assignee) ? $assignee : null;

        $items = $useCase->execute($tripId, $assigneeStr);

        return response()->json([
            'data' => array_map(fn ($item) => $item->toArray(), $items),
        ]);
    }

    /**
     * POST /api/trips/{tripId}/packing
     */
    public function store(int $tripId, StorePackingItemRequest $request, CreatePackingItemUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        $item = $useCase->execute(
            tripId: $tripId,
            userId: $user->id,
            name: $validated['name'],
            assignee: $validated['assignee'] ?? 'shared',
            category: $validated['category'] ?? null,
            sortOrder: $validated['sort_order'] ?? null,
        );

        return response()->json(['data' => $item->toArray()], 201);
    }

    /**
     * PATCH /api/trips/{tripId}/packing/{id}
     */
    public function update(int $tripId, int $id, UpdatePackingItemRequest $request, UpdatePackingItemUseCase $useCase): JsonResponse
    {
        $result = $useCase->execute($tripId, $id, $request->validated());

        if ($result === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json(['data' => $result->toArray()]);
    }

    /**
     * DELETE /api/trips/{tripId}/packing/{id}
     */
    public function destroy(int $tripId, int $id, DeletePackingItemUseCase $useCase): Response
    {
        $useCase->execute($tripId, $id);

        return response()->noContent();
    }
}
