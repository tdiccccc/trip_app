<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

final class Trip
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $destination,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly ?string $coverImageUrl,
        public readonly int $createdBy,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {}
}
