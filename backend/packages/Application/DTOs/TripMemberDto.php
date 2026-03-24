<?php

declare(strict_types=1);

namespace Packages\Application\DTOs;

use Packages\Domain\Entities\TripMember;

final readonly class TripMemberDto
{
    public function __construct(
        public int $id,
        public int $tripId,
        public int $userId,
        public ?string $userName,
        public string $role,
        public ?string $joinedAt,
    ) {}

    public static function fromEntity(TripMember $member, ?string $userName = null): self
    {
        return new self(
            id: $member->id,
            tripId: $member->tripId,
            userId: $member->userId,
            userName: $userName,
            role: $member->role->value,
            joinedAt: $member->joinedAt,
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
            'role' => $this->role,
            'joined_at' => $this->joinedAt,
        ];
    }
}
