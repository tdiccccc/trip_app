<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpotMemoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Packages\Application\UseCases\Album\GetAlbumUseCase;
use Packages\Application\UseCases\Spot\CreateSpotMemoUseCase;
use Packages\Application\UseCases\Spot\GetSpotDetailUseCase;
use Packages\Application\UseCases\Spot\GetSpotListUseCase;
use Packages\Domain\Enums\SpotCategory;

final class SpotController extends Controller
{
    /**
     * GET /api/trips/{tripId}/spots
     */
    public function index(int $tripId, Request $request, GetSpotListUseCase $useCase): JsonResponse
    {
        $category = $request->query('category');
        $spotCategory = is_string($category) ? SpotCategory::from($category) : null;

        $spots = $useCase->execute($tripId, $spotCategory);

        return response()->json([
            'data' => array_map(fn ($s) => $s->toArray(), $spots),
        ]);
    }

    /**
     * GET /api/trips/{tripId}/spots/{id}
     */
    public function show(int $tripId, int $id, GetSpotDetailUseCase $useCase): JsonResponse
    {
        $result = $useCase->execute($tripId, $id);

        if ($result === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json([
            'data' => [
                ...$result['spot']->toArray(),
                'memos' => array_map(fn ($m) => $m->toArray(), $result['memos']),
                'photos' => array_map(fn ($p) => $p->toArray(), $result['photos']),
            ],
        ]);
    }

    /**
     * POST /api/trips/{tripId}/spots/{id}/memos
     */
    public function storeMemo(int $tripId, int $id, StoreSpotMemoRequest $request, CreateSpotMemoUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $memo = $useCase->execute($tripId, $id, $user->id, $request->validated('body'));

        return response()->json(['data' => $memo->toArray()], 201);
    }

    /**
     * GET /api/trips/{tripId}/spots/{id}/photos
     */
    public function photos(int $tripId, int $id, GetAlbumUseCase $useCase): JsonResponse
    {
        $photos = $useCase->execute(tripId: $tripId, spotId: $id);

        return response()->json([
            'data' => array_map(fn ($p) => $p->toArray(), $photos),
        ]);
    }
}
