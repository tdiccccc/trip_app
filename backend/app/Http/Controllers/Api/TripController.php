<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Packages\Application\UseCases\Trip\CreateTripUseCase;
use Packages\Application\UseCases\Trip\DeleteTripUseCase;
use Packages\Application\UseCases\Trip\GetTripDetailUseCase;
use Packages\Application\UseCases\Trip\GetTripListUseCase;
use Packages\Application\UseCases\Trip\UpdateTripUseCase;

final class TripController extends Controller
{
    /**
     * GET /api/trips
     */
    public function index(Request $request, GetTripListUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $trips = $useCase->execute($user->id);

        return response()->json([
            'data' => array_map(fn ($t) => $t->toArray(), $trips),
        ]);
    }

    /**
     * POST /api/trips
     */
    public function store(StoreTripRequest $request, CreateTripUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        $trip = $useCase->execute(
            userId: $user->id,
            title: $validated['title'],
            description: $validated['description'] ?? null,
            destination: $validated['destination'] ?? null,
            startDate: $validated['start_date'],
            endDate: $validated['end_date'],
            memberIds: $validated['member_ids'] ?? [],
        );

        return response()->json(['data' => $trip->toArray()], 201);
    }

    /**
     * GET /api/trips/{tripId}
     */
    public function show(Request $request, int $tripId, GetTripDetailUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $result = $useCase->execute($tripId, $user->id);

        if ($result === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json(['data' => $result->toArray()]);
    }

    /**
     * PATCH /api/trips/{tripId}
     */
    public function update(UpdateTripRequest $request, int $tripId, UpdateTripUseCase $useCase): JsonResponse
    {
        $result = $useCase->execute($tripId, $request->validated());

        if ($result === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json(['data' => $result->toArray()]);
    }

    /**
     * DELETE /api/trips/{tripId}
     */
    public function destroy(Request $request, int $tripId, DeleteTripUseCase $useCase): Response
    {
        $useCase->execute($tripId);

        return response()->noContent();
    }
}
