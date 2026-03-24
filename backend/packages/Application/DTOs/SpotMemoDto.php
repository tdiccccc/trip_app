<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\SpotMemo;

final readonly class SpotMemoDto
{
    public function __construct(
        public int $id,
        public int $spotId,
        public int $userId,
        public string $body,
    ) {}

    public static function fromEntity(SpotMemo $memo): self
    {
        return new self(
            id: $memo->id,
            spotId: $memo->spotId,
            userId: $memo->userId,
            body: $memo->body,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'spot_id' => $this->spotId,
            'user_id' => $this->userId,
            'body' => $this->body,
        ];
    }
}
