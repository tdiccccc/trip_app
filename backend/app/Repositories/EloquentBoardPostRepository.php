<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BoardPost as BoardPostModel;
use Packages\Domain\Entities\BoardPost;
use Packages\Domain\Repositories\BoardPostRepositoryInterface;

class EloquentBoardPostRepository implements BoardPostRepositoryInterface
{
    /**
     * @return BoardPost[]
     */
    public function findAll(int $tripId): array
    {
        return BoardPostModel::query()
            ->where('trip_id', $tripId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (BoardPostModel $model): BoardPost => $this->toEntity($model))
            ->all();
    }

    public function findById(int $id): ?BoardPost
    {
        $model = BoardPostModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function save(BoardPost $post): BoardPost
    {
        $model = $post->id === 0
            ? new BoardPostModel
            : BoardPostModel::findOrFail($post->id);

        $model->fill([
            'trip_id' => $post->tripId,
            'user_id' => $post->userId,
            'body' => $post->body,
            'photo_id' => $post->photoId,
            'is_best_shot' => $post->isBestShot,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    private function toEntity(BoardPostModel $model): BoardPost
    {
        return new BoardPost(
            id: $model->id,
            tripId: $model->trip_id,
            userId: $model->user_id,
            body: $model->body,
            photoId: $model->photo_id,
            isBestShot: $model->is_best_shot,
        );
    }
}
