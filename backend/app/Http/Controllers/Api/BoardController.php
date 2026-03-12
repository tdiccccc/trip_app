<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBoardPostRequest;
use App\Http\Requests\StoreReactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Packages\Application\UseCases\Board\AddReactionUseCase;
use Packages\Application\UseCases\Board\DeleteBoardPostUseCase;
use Packages\Application\UseCases\Board\GetBoardPostsUseCase;
use Packages\Application\UseCases\Board\PostMessageUseCase;
use Packages\Domain\Exceptions\DomainException;
use Packages\Domain\Exceptions\UnauthorizedOperationException;
use Packages\Domain\Repositories\BoardPostRepositoryInterface;

final class BoardController extends Controller
{
    /**
     * GET /api/trips/{tripId}/board
     */
    public function index(int $tripId, GetBoardPostsUseCase $useCase): JsonResponse
    {
        $results = $useCase->execute($tripId);

        $data = array_map(fn ($item) => [
            ...$item['post']->toArray(),
            'reactions' => array_map(fn ($r) => $r->toArray(), $item['reactions']),
        ], $results);

        return response()->json(['data' => $data]);
    }

    /**
     * POST /api/trips/{tripId}/board
     */
    public function store(int $tripId, StoreBoardPostRequest $request, PostMessageUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $validated = $request->validated();

        $post = $useCase->execute(
            tripId: $tripId,
            userId: $user->id,
            body: $validated['body'],
            photoId: $validated['photo_id'] ?? null,
            isBestShot: (bool) ($validated['is_best_shot'] ?? false),
        );

        return response()->json(['data' => $post->toArray()], 201);
    }

    /**
     * POST /api/trips/{tripId}/board/{id}/reactions
     */
    public function storeReaction(
        int $tripId,
        int $id,
        StoreReactionRequest $request,
        AddReactionUseCase $useCase,
        BoardPostRepositoryInterface $boardPostRepository,
    ): JsonResponse {
        $post = $boardPostRepository->findById($id);

        if ($post === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $reaction = $useCase->execute(
                tripId: $tripId,
                boardPostId: $id,
                userId: $user->id,
                emoji: $request->validated('emoji'),
            );

            return response()->json(['data' => $reaction->toArray()], 201);
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => [
                    'emoji' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    /**
     * DELETE /api/trips/{tripId}/board/{id}
     */
    public function destroy(int $tripId, int $id, Request $request, DeleteBoardPostUseCase $useCase): JsonResponse|Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $useCase->execute($tripId, $id, $user->id);
        } catch (UnauthorizedOperationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->noContent();
    }
}
