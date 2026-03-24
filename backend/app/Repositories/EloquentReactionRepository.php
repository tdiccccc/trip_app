<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reaction as ReactionModel;
use Packages\Domain\Entities\Reaction;
use Packages\Domain\Repositories\ReactionRepositoryInterface;

class EloquentReactionRepository implements ReactionRepositoryInterface
{
    /**
     * @return Reaction[]
     */
    public function findByBoardPostId(int $boardPostId): array
    {
        return ReactionModel::query()
            ->where('board_post_id', $boardPostId)
            ->get()
            ->map(fn (ReactionModel $model): Reaction => $this->toEntity($model))
            ->all();
    }

    public function save(Reaction $reaction): Reaction
    {
        $model = $reaction->id === 0
            ? new ReactionModel
            : ReactionModel::findOrFail($reaction->id);

        $model->fill([
            'board_post_id' => $reaction->boardPostId,
            'user_id' => $reaction->userId,
            'emoji' => $reaction->emoji,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    public function existsByPostUserEmoji(int $boardPostId, int $userId, string $emoji): bool
    {
        return ReactionModel::query()
            ->where('board_post_id', $boardPostId)
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->exists();
    }

    private function toEntity(ReactionModel $model): Reaction
    {
        return new Reaction(
            id: $model->id,
            boardPostId: $model->board_post_id,
            userId: $model->user_id,
            emoji: $model->emoji,
        );
    }
}
