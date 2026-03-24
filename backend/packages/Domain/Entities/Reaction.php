<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

final class Reaction
{
    public function __construct(
        public readonly int $id,
        public readonly int $boardPostId,
        public readonly int $userId,
        public readonly string $emoji,
    ) {}
}
