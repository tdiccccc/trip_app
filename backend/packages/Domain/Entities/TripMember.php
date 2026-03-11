<?php

declare(strict_types=1);

namespace Packages\Domain\Entities;

use Packages\Domain\Enums\TripMemberRole;

final class TripMember
{
    public function __construct(
        public readonly int $id,
        public readonly int $tripId,
        public readonly int $userId,
        public readonly TripMemberRole $role,
        public readonly ?string $joinedAt,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }
}
