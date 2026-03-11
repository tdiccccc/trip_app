<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\BoardPost;

final readonly class BoardPostDto
{
    public function __construct(
        public int $id,
        public int $tripId,
        public int $userId,
        public string $body,
        public ?int $photoId,
        public bool $isBestShot,
    ) {
    }

    public static function fromEntity(BoardPost $post): self
    {
        return new self(
            id: $post->id,
            tripId: $post->tripId,
            userId: $post->userId,
            body: $post->body,
            photoId: $post->photoId,
            isBestShot: $post->isBestShot,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'trip_id' => $this->tripId,
            'user_id' => $this->userId,
            'body' => $this->body,
            'photo_id' => $this->photoId,
            'is_best_shot' => $this->isBestShot,
        ];
    }
}
