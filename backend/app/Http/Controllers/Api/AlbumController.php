<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Packages\Application\UseCases\Album\DeletePhotoUseCase;
use Packages\Application\UseCases\Album\GetAlbumUseCase;
use Packages\Application\UseCases\Album\UploadPhotoUseCase;
use Packages\Domain\Exceptions\UnauthorizedOperationException;

final class AlbumController extends Controller
{
    /**
     * GET /api/trips/{tripId}/photos
     */
    public function index(int $tripId, Request $request, GetAlbumUseCase $useCase): JsonResponse
    {
        $spotId = $request->query('spot_id');
        $sort = $request->query('sort', 'taken_at');
        $order = $request->query('order', 'desc');

        $photos = $useCase->execute(
            tripId: $tripId,
            spotId: is_numeric($spotId) ? (int) $spotId : null,
            sort: is_string($sort) ? $sort : 'taken_at',
            order: is_string($order) ? $order : 'desc',
        );

        return response()->json([
            'data' => array_map(fn ($p) => $p->toArray(), $photos),
        ]);
    }

    /**
     * POST /api/trips/{tripId}/photos
     */
    public function store(int $tripId, StorePhotoRequest $request, UploadPhotoUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $file = $request->file('photo');
        $validated = $request->validated();

        $extension = $file->getClientOriginalExtension();
        $storagePath = sprintf('photos/%d/%s.%s', $user->id, Str::uuid()->toString(), $extension);

        Storage::disk('s3')->put($storagePath, $file->getContent());

        $photo = $useCase->execute(
            tripId: $tripId,
            userId: $user->id,
            spotId: $validated['spot_id'] ?? null,
            storagePath: $storagePath,
            thumbnailPath: null,
            originalFilename: $file->getClientOriginalName(),
            mimeType: $file->getMimeType() ?? $file->getClientMimeType(),
            fileSize: $file->getSize(),
            caption: $validated['caption'] ?? null,
            takenAt: $validated['taken_at'] ?? null,
        );

        return response()->json(['data' => $photo->toArray()], 201);
    }

    /**
     * DELETE /api/trips/{tripId}/photos/{id}
     */
    public function destroy(int $tripId, int $id, Request $request, DeletePhotoUseCase $useCase): JsonResponse|Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $useCase->execute($tripId, $id, $user->id);
        } catch (UnauthorizedOperationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->noContent();
    }
}
