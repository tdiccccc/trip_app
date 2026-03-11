<?php

declare(strict_types=1);

namespace Packages\Domain\Repositories;

use Packages\Domain\Entities\Reaction;

interface ReactionRepositoryInterface
{
    /**
     * @return Reaction[]
     */
    public function findByBoardPostId(int $boardPostId): array;

    public function save(Reaction $reaction): Reaction;

    public function existsByPostUserEmoji(int $boardPostId, int $userId, string $emoji): bool;
}
