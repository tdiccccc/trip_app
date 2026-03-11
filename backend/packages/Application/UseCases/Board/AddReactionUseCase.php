<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Board;

use Packages\Application\DTOs\ReactionDto;
use Packages\Domain\Entities\Reaction;
use Packages\Domain\Exceptions\DomainException;
use Packages\Domain\Repositories\ReactionRepositoryInterface;

final class AddReactionUseCase
{
    public function __construct(
        private readonly ReactionRepositoryInterface $reactionRepository,
    ) {
    }

    public function execute(int $boardPostId, int $userId, string $emoji): ReactionDto
    {
        if ($this->reactionRepository->existsByPostUserEmoji($boardPostId, $userId, $emoji)) {
            throw new DomainException('この絵文字は既にリアクション済みです。');
        }

        $reaction = new Reaction(
            id: 0,
            boardPostId: $boardPostId,
            userId: $userId,
            emoji: $emoji,
        );

        $saved = $this->reactionRepository->save($reaction);

        return ReactionDto::fromEntity($saved);
    }
}
