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
        public ?string $createdAt = null,
        public ?string $userName = null,
    ) {
    }

    public static function fromEntity(BoardPost $post, ?string $userName = null): self
    {
        return new self(
            id: $post->id,
            tripId: $post->tripId,
            userId: $post->userId,
            body: $post->body,
            photoId: $post->photoId,
            isBestShot: $post->isBestShot,
            createdAt: $post->createdAt,
            userName: $userName,
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
            'user_name' => $this->userName,
            'body' => $this->body,
            'photo_id' => $this->photoId,
            'is_best_shot' => $this->isBestShot,
            'created_at' => $this->createdAt,
        ];
    }
}
