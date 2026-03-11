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
     * GET /api/spots
     */
    public function index(Request $request, GetSpotListUseCase $useCase): JsonResponse
    {
        $category = $request->query('category');
        $spotCategory = is_string($category) ? SpotCategory::from($category) : null;

        $spots = $useCase->execute($spotCategory);

        return response()->json([
            'data' => array_map(fn ($s) => $s->toArray(), $spots),
        ]);
    }

    /**
     * GET /api/spots/{id}
     */
    public function show(int $id, GetSpotDetailUseCase $useCase): JsonResponse
    {
        $result = $useCase->execute($id);

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
     * POST /api/spots/{id}/memos
     */
    public function storeMemo(int $id, StoreSpotMemoRequest $request, CreateSpotMemoUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $memo = $useCase->execute($id, $user->id, $request->validated('body'));

        return response()->json(['data' => $memo->toArray()], 201);
    }

    /**
     * GET /api/spots/{id}/photos
     */
    public function photos(int $id, GetAlbumUseCase $useCase): JsonResponse
    {
        $photos = $useCase->execute(spotId: $id);

        return response()->json([
            'data' => array_map(fn ($p) => $p->toArray(), $photos),
        ]);
    }
}
