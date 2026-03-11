<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReorderItineraryRequest;
use App\Http\Requests\StoreItineraryItemRequest;
use App\Http\Requests\UpdateItineraryItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Packages\Application\UseCases\Itinerary\CreateItineraryItemUseCase;
use Packages\Application\UseCases\Itinerary\DeleteItineraryItemUseCase;
use Packages\Application\UseCases\Itinerary\GetItineraryUseCase;
use Packages\Application\UseCases\Itinerary\ReorderItineraryUseCase;
use Packages\Application\UseCases\Itinerary\UpdateItineraryItemUseCase;

final class ItineraryController extends Controller
{
    /**
     * GET /api/trips/{tripId}/itinerary
     */
    public function index(int $tripId, Request $request, GetItineraryUseCase $useCase): JsonResponse
    {
        $date = $request->query('date');
        $dateStr = is_string($date) ? $date : null;

        $items = $useCase->execute($tripId, $dateStr);

        return response()->json([
            'data' => array_map(fn ($item) => $item->toArray(), $items),
        ]);
    }

    /**
     * POST /api/trips/{tripId}/itinerary
     */
    public function store(int $tripId, StoreItineraryItemRequest $request, CreateItineraryItemUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        $item = $useCase->execute(
            tripId: $tripId,
            userId: $user->id,
            spotId: $validated['spot_id'] ?? null,
            title: $validated['title'],
            memo: $validated['memo'] ?? null,
            date: $validated['date'],
            startTime: $validated['start_time'] ?? null,
            endTime: $validated['end_time'] ?? null,
            transport: $validated['transport'] ?? null,
            sortOrder: $validated['sort_order'] ?? null,
        );

        return response()->json(['data' => $item->toArray()], 201);
    }

    /**
     * PATCH /api/trips/{tripId}/itinerary/{id}
     */
    public function update(int $tripId, int $id, UpdateItineraryItemRequest $request, UpdateItineraryItemUseCase $useCase): JsonResponse
    {
        $result = $useCase->execute($tripId, $id, $request->validated());

        if ($result === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json(['data' => $result->toArray()]);
    }

    /**
     * DELETE /api/trips/{tripId}/itinerary/{id}
     */
    public function destroy(int $tripId, int $id, DeleteItineraryItemUseCase $useCase): Response
    {
        $useCase->execute($tripId, $id);

        return response()->noContent();
    }

    /**
     * PATCH /api/trips/{tripId}/itinerary/reorder
     */
    public function reorder(int $tripId, ReorderItineraryRequest $request, ReorderItineraryUseCase $useCase): JsonResponse
    {
        /** @var array<array{id: int, sort_order: int}> $items */
        $items = $request->validated('items');

        $useCase->execute($tripId, $items);

        return response()->json(['message' => 'Reordered successfully.']);
    }
}
