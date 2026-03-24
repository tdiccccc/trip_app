<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

use Packages\Domain\Enums\Assignee;

final class PackingItem
{
    public function __construct(
        public readonly int $id,
        public readonly int $tripId,
        public readonly int $userId,
        public readonly string $name,
        public readonly bool $isChecked,
        public readonly Assignee $assignee,
        public readonly ?string $category,
        public readonly int $sortOrder,
    ) {}
}
