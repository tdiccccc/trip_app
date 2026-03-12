<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

use Packages\Domain\ValueObjects\Money;

final class Expense
{
    public function __construct(
        public readonly int $id,
        public readonly int $tripId,
        public readonly int $userId,
        public readonly string $description,
        public readonly Money $amount,
        public readonly int $categoryId,
        public readonly string $paidAt,
        public readonly bool $isShared,
    ) {
    }
}
