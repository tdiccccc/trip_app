<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

final class SpotMemo
{
    public function __construct(
        public readonly int $id,
        public readonly int $spotId,
        public readonly int $userId,
        public readonly string $body,
    ) {}
}
