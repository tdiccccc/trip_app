<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

final class BoardPost
{
    public function __construct(
        public readonly int $id,
        public readonly int $tripId,
        public readonly int $userId,
        public readonly string $body,
        public readonly ?int $photoId,
        public readonly bool $isBestShot,
        public readonly ?string $createdAt = null,
    ) {
    }
}
