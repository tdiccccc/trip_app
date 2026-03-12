<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

final class ExpenseCategory
{
    public function __construct(
        public readonly int $id,
        public readonly int $tripId,
        public readonly string $name,
        public readonly string $key,
        public readonly ?string $color,
        public readonly int $sortOrder,
    ) {
    }
}
