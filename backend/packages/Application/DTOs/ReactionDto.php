<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\Reaction;

final readonly class ReactionDto
{
    public function __construct(
        public int $id,
        public int $boardPostId,
        public int $userId,
        public string $emoji,
    ) {
    }

    public static function fromEntity(Reaction $reaction): self
    {
        return new self(
            id: $reaction->id,
            boardPostId: $reaction->boardPostId,
            userId: $reaction->userId,
            emoji: $reaction->emoji,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'board_post_id' => $this->boardPostId,
            'user_id' => $this->userId,
            'emoji' => $this->emoji,
        ];
    }
}
